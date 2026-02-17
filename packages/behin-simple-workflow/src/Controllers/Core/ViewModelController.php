<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Entity;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\ViewModel;
use BehinFileControl\Controllers\FileController;
use BehinUserRoles\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ViewModelController extends Controller
{
    public function index()
    {
        $viewModels = ViewModel::orderBy('created_at')->get();
        return view('SimpleWorkflowView::Core.ViewModel.index', compact('viewModels'));
    }

    public function create()
    {
        $entities = EntityController::getAll();
        $forms = FormController::getAll();
        $scripts = ScriptController::getAll();
        return view('SimpleWorkflowView::Core.ViewModel.create', compact('entities', 'forms', 'scripts'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['api_key'] = $request->api_key ? $request->api_key : Str::random(16);
        $viewModel = ViewModel::create($data);
        return redirect()->back()->with(['success' => trans('Created Successfully')]);
    }

    public function edit(ViewModel $view_model)
    {
        $entities = EntityController::getAll();
        $forms = FormController::getAll();
        $scripts = ScriptController::getAll();
        return view('SimpleWorkflowView::Core.ViewModel.edit', compact('view_model', 'entities', 'forms', 'scripts'));
    }

    public function update(Request $request, ViewModel $view_model)
    {
        $data = $request->except('_token');
        $data['api_key'] = $request->api_key ? $request->api_key : Str::random(16);
        $nullableArrayFields = [
            'which_rows_user_can_delete',
            'which_rows_user_can_update',
            'which_rows_user_can_read'
        ];

        foreach ($nullableArrayFields as $field) {
            $data[$field] = $request->has($field) ? $request->$field : [];
        }

        $view_model->update($data);
        return redirect()->back()->with(['success' => trans('Updated Successfully')]);
    }

    public function copy(ViewModel $view_model)
    {
        // کپی اطلاعات رکورد
        $newViewModel = $view_model->replicate();

        // در صورت نیاز، فیلدهایی که باید منحصر به‌فرد باشن رو تغییر بده (مثلاً نام یا شناسه)
        $newViewModel->name = $newViewModel->name . ' (Copy)';

        // ذخیره رکورد جدید
        $newViewModel->save();

        return redirect()->back()->with(['success' => trans('fields.Copy Successfully')]);
    }

    public function export(Request $request)
    {
        $ids = $request->input('view_model_ids', []);
        if (empty($ids)) {
            return redirect()->route('simpleWorkflow.view-model.index')->with('error', 'No view models selected for export.');
        }
        $viewModels = ViewModel::whereIn('id', $ids)->get();
        $fileName = 'view-models-' . date('Ymd_His') . '.json';

        if ($viewModels->count() === 1) {
            $content = $viewModels->first()->toJson(JSON_PRETTY_PRINT);
        } else {
            $content = $viewModels->toJson(JSON_PRETTY_PRINT);
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'view_models_file' => 'required|file',
        ]);

        $content = file_get_contents($request->file('view_models_file')->getRealPath());
        $data = json_decode($content, true);

        if (is_null($data)) {
            return redirect()->route('simpleWorkflow.view-model.index')->with('error', 'Invalid import file.');
        }

        $items = isset($data[0]) ? $data : [$data];
        $fillable = (new ViewModel())->getFillable();

        foreach ($items as $item) {
            $attributes = [];
            foreach ($fillable as $field) {
                if (array_key_exists($field, $item)) {
                    $attributes[$field] = $item[$field];
                }
            }

            $id = $item['id'] ?? Str::uuid()->toString();
            $attributes['id'] = $id;

            ViewModel::updateOrCreate(['id' => $id], $attributes);
        }

        return redirect()->route('simpleWorkflow.view-model.index')->with('success', 'View models imported successfully.');
    }


    public static function getById($id)
    {
        return ViewModel::find($id);
    }

    public function resolveColumnPath($model, string $columnPath)
    {
        try {
            $parts = explode('()->', $columnPath);
            $current = $model;

            foreach ($parts as $index => $part) {
                if (!$current) {
                    return null;
                }

                if ($index === count($parts) - 1) {
                    return $current->$part ?? null;
                }

                // استفاده از property به جای method
                $current = $current->$part();
            }

            return null;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public static function userCanUpdateRow($row, $updateCondition)
    {
        if (
            in_array('all', $updateCondition) ||
            (in_array('user-created-it', $updateCondition) && $row->created_by == Auth::id()) ||
            (in_array('user-contributed-it', $updateCondition) && in_array(Auth::id(), explode(',', $row->contributers ?? ''))) ||
            (in_array('user-updated-it', $updateCondition) && $row->updated_by == Auth::id())
        ) {
            return true;
        }

        return false;
    }

    public static function userCanDeleteRow($row, $deleteCondition)
    {
        if (
            in_array('all', $deleteCondition) ||
            (in_array('user-created-it', $deleteCondition) && $row->created_by == Auth::id()) ||
            (in_array('user-contributed-it', $deleteCondition) && in_array(Auth::id(), explode(',', $row->contributers ?? ''))) ||
            (in_array('user-updated-it', $deleteCondition) && $row->updated_by == Auth::id())
        ) {
            return true;
        }

        return false;
    }


    public function getRows(Request $request)
    {
        try {

            // $inbox = InboxController::getById($request->inbox_id);
            $case = CaseController::getById($request->case_id);
            $viewModel = self::getById($request->viewModel_id);

            if ($viewModel->api_key != $request->api_key) {
                return response(trans("fields.Api key is not valid"), 403);
            }

            $columns = explode(',', $viewModel->default_fields);
            $max_number_of_rows = $viewModel->max_number_of_rows;

            // ✅ تبدیل ایمن به آرایه
            $readCondition = is_array($viewModel->which_rows_user_can_read)
                ? $viewModel->which_rows_user_can_read
                : json_decode($viewModel->which_rows_user_can_read, true) ?? [];

            $updateCondition = is_array($viewModel->which_rows_user_can_update)
                ? $viewModel->which_rows_user_can_update
                : json_decode($viewModel->which_rows_user_can_update, true) ?? [];

            $deleteCondition = is_array($viewModel->which_rows_user_can_delete)
                ? $viewModel->which_rows_user_can_delete
                : json_decode($viewModel->which_rows_user_can_delete, true) ?? [];

            $model = self::getModelById($viewModel->id);
            $s = '';

            if ($viewModel->allow_read_row) {
                if ($viewModel->show_rows_based_on == 'case_id') {
                    $rows = $model::where('case_id', $case->id)->whereNull('deleted_at');
                } elseif ($viewModel->show_rows_based_on == 'case_number') {
                    $rows = $model::where('case_number', $case->number)->whereNull('deleted_at');
                } else {
                    $rows = $model::query()->whereNull('deleted_at');
                }

                $rows = $rows->where(function ($query) use ($readCondition) {
                    if (in_array('all', $readCondition)) {
                        // $query->orWhereNotNull('deleted_at');
                    }

                    if (in_array('user-created-it', $readCondition)) {
                        $query->orWhere('created_by', Auth::id());
                    }

                    if (in_array('user-contributed-it', $readCondition)) {
                        $query->orWhereRaw('FIND_IN_SET(?, contributers)', [Auth::id()]);
                    }

                    if (in_array('user-updated-it', $readCondition)) {
                        $query->orWhere('updated_by', Auth::id());
                    }
                });

                $rows = $rows->orderBy('updated_at', 'desc')
                    // ->take($max_number_of_rows)
                    ->get()->each(function ($row) use ($viewModel, $updateCondition, $deleteCondition) {
                        $row->show_as = $viewModel->show_as;
                        $row->allow_update = self::userCanUpdateRow($row, $updateCondition);
                        $row->allow_delete = self::userCanDeleteRow($row, $deleteCondition);
                    });
                if ($viewModel->script_before_show_rows) {
                    $request->merge(['rows' => $rows]);
                    $rows = ScriptController::runFromView($request, $viewModel->script_before_show_rows);
                    Log::info($rows);
                }

                foreach ($rows as $row) {
                    if ($row->show_as == 'table') {
                        $s .= "<tr>";
                        foreach ($columns as $column) {
                            $column = trim($column);
                            try {
                                if (str_contains($column, '()->')) {
                                    $value = $this->resolveColumnPath($row, $column);
                                } elseif (Str::endsWith($column, '()')) {
                                    $method = Str::beforeLast($column, '()');

                                    if ($method && method_exists($row, $method)) {
                                        $value = $row->$method();
                                    } else {
                                        $value = 'تابع تعریف نشده است';
                                    }
                                } else {
                                    $value = $row->$column ?? null;
                                }

                                $s .= "<td>{$value}</td>";
                            } catch (\Throwable $e) {
                                $s .= "<td>" . $e->getMessage() . "</td>";
                            }
                        }
                        $s .= "<td>";
                        if ($row->allow_update) {
                            $s .= "<i class='fa fa-edit btn btn-sm btn-success ml-1' onclick='open_view_model_form(`$viewModel->update_form`, `$viewModel->id`,`$row->id`, `$viewModel->api_key`)'></i>";
                        }

                        if ($row->allow_delete) {
                            $s .= "<i class='fa fa-trash btn btn-sm btn-danger ml-1' onclick='delete_view_model_row(`$viewModel->id`,`$row->id`, `$viewModel->api_key`)'></i>";
                        }
                        $s .= "</td>";
                        $s .= "</tr>";
                    }
                    if ($row->show_as == 'box') {
                        $request = new Request([
                            'api_key' => $viewModel->api_key,
                            'row_id' => $row->id,
                            'case_id' => $request->case_id,
                            'viewModel_id' => $viewModel->id,
                        ]);
                        $s .= "<div class='card'>";
                        if ($row->allow_update) {
                            $s .= FormController::open($request, $viewModel->update_form, false);
                        } else {
                            $s .= FormController::openReadForm($request, $viewModel->read_form, false);
                        }
                        $s .= "</div>";
                    }
                }
            }

            //
            if ($viewModel->allow_create_row and count($rows) < $max_number_of_rows) {
                $s .= "<tr>";
                $colspan = count($columns) + 1;
                $btnLabel = trans('fields.Create new');
                $s .= "<td colspan='{$colspan}'>";
                $s .= "<button class='btn btn-sm btn-primary' onclick='open_view_model_create_new_form(`$viewModel->create_form`, `$viewModel->id`, `$viewModel->api_key`)'>";
                $s .= "<i class='fa fa-plus' aria-hidden='true'></i>{$btnLabel}</button></td>";
                $s .= "</tr>";
            }
            return $s;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function updateRecord(Request $request)
    {
        try {
            $case = CaseController::getById($request->caseId);
            $viewModel = self::getById($request->viewModelId);

            if ($viewModel->api_key != $request->api_key) {
                return response(trans("fields.Api key is not valid"), 403);
            }

            $model = self::getModelById($viewModel->id);

            $row = $model::findOrNew($request->rowId);

            $isNew = !$row->exists;
            $rows = $model::where('case_number', $case->number)->count();
            if ($rows >= $viewModel->max_number_of_rows) {
                return response(trans("حداکثر تعداد رکورد مجاز " . $viewModel->max_number_of_rows . " رکورد است"), 403);
            }
            $data = $request->all();
            // بررسی داینامیک فایل‌ها
            foreach ($request->allFiles() as $fieldName => $file) {
                $savedPaths = [];
                $path = FileController::store($file, 'simpleWorkflow');
                if ($path['status'] == 200) {
                    $data[$fieldName] = $path['dir'];
                }
            }
            // $data[$fieldName] = $savedPaths;

            if ($isNew && $viewModel->script_before_create) {
                $request->merge(['rowData' => $data]);

                $result = ScriptController::runFromView($request, $viewModel->script_before_create);

                if ($result) {
                    return $result;
                }

                $data = $request->all();
                if (isset($data['rowData']) && is_array($data['rowData'])) {
                    $data = array_merge($data, $data['rowData']);
                }
                unset($data['rowData']);
                if ($request->has('rowData')) {
                    $request->request->remove('rowData');
                }
            }

            $fillable = $row->getFillable();
            foreach ($data as $key => $value) {
                if (in_array($key, $fillable)) {
                    $row->$key = $value;
                }
            }

            if ($isNew) {
                if (in_array('case_id', $fillable)) {
                    $row->case_id = $case->id;
                }
                if (in_array('case_number', $fillable)) {
                    $row->case_number = $case->number;
                }
                $row->created_by = Auth::id();
                $row->contributers = Auth::id();
            }
            $row->updated_by = Auth::id();

            // اضافه کردن کاربر فعلی به contributers (بدون تکرار)
            $contribs = explode(',', $row->contributers ?? '');
            if (!in_array(Auth::id(), $contribs)) {
                $contribs[] = Auth::id();
            }
            $row->contributers = implode(',', array_filter($contribs));

            $row->save();

            if ($isNew && $viewModel->script_after_create) {
                $request->merge(['rowId' => $row->id]);

                $result = ScriptController::runFromView($request, $viewModel->script_after_create);

                if ($result) {
                    return $result;
                }
            }

            if (!$isNew && $viewModel->script_after_update) {
                $request->merge(['rowId' => $row->id]);

                $result = ScriptController::runFromView($request, $viewModel->script_after_update);

                if ($result) {
                    return $result;
                }
            }
        } catch (Exception $th) {
            return response($th->getMessage(), 500);
        }


        return response(trans('fields.updated'));
    }

    public function deleteRecord(Request $request)
    {
        $viewModel = self::getById($request->viewModel_id);
        if ($viewModel->api_key != $request->api_key) {
            return response(trans("fields.Api key is not valid"), 403);
        }

        $model = self::getModelById($viewModel->id);

        $row = $model::find($request->row_id);

        $row->delete();

        return response(trans('fields.deleted'));
    }


    public static function getModelById($id)
    {
        $viewModel = ViewModel::find($id);
        $entity = Entity::find($viewModel->entity_id);
        $tableNamespace = $entity->namespace;
        $tableModelName = $entity->model_name;
        $model = "\\" . $tableNamespace . "\\" . $tableModelName;
        return $model;
    }
}

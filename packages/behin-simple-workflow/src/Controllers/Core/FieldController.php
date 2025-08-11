<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Fields;
use Behin\SimpleWorkflow\Models\Core\ViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FieldController extends Controller
{
    public function index()
    {
        $fields = self::getAll();
        return view('SimpleWorkflowView::Core.Field.index', compact('fields'));
    }

    public function create()
    {
        return view('SimpleWorkflowView::Core.Condition.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'query' => 'nullable|string',
            'placeholder' => 'nullable|string',
        ]);
        // $attributes = [
        //     'query' => $request->input('query') ? $request->input('query') : null,
        //     'placeholder' => $request->placeholder,
        //     'style' => $request->style,
        //     'script' => $request->script,
        //     'datalist_from_database' => $request->datalist_from_database
        // ];

        Fields::create([
            'name' => $request->name,
            'type' => $request->type,
            'attributes' => null //json_encode($attributes)
        ]);

        return redirect()->route('simpleWorkflow.fields.index', ['#createForm'])->with('success', 'Fields created successfully.');
    }

    public function edit(Fields $field)
    {
        $viewModels = ViewModel::all();
        return view('SimpleWorkflowView::Core.Field.edit', compact('field', 'viewModels'));
    }

    public function update(Request $request, Fields $field)
    {

        $attributes = [
            'query' => $request->input('query') ? $request->input('query') : null,
            'placeholder' => $request->placeholder,
            'options' => $request->options ? $request->options : null,
            'style' => $request->style,
            'script' => $request->script,
            'datalist_from_database' => $request->datalist_from_database,
            'view_model_id' => $request->view_model_id
        ];
        if ($request->columns !== null) {
            $attributes['columns'] = $request->columns;
        }
        if ($request->id !== null) {
            $attributes['id'] = $request->id;
        }
        $field->update([
            'name' => $request->name,
            'type' => $request->type,
            'attributes' => json_encode($attributes)
        ]);

        return redirect()->route('simpleWorkflow.fields.edit', $field->id)->with('success', 'Fields updated successfully.');
    }

    public function copy(Fields $field)
    {
        // کپی اطلاعات رکورد
        $field = $field->replicate();

        // در صورت نیاز، فیلدهایی که باید منحصر به‌فرد باشن رو تغییر بده (مثلاً نام یا شناسه)
        $field->name = $field->name . ' (Copy)';

        // ذخیره رکورد جدید
        $field->save();

        return $this->edit($field);
    }

    public function export(Request $request)
    {
        $ids = $request->input('field_ids', []);
        if (empty($ids)) {
            return redirect()->route('simpleWorkflow.fields.index')->with('error', 'No fields selected for export.');
        }
        $fields = Fields::whereIn('id', $ids)->get();
        $fileName = 'fields-' . date('Ymd_His') . '.json';
        if ($fields->count() === 1) {
            $content = $fields->first()->toJson(JSON_PRETTY_PRINT);
        } else {
            $content = $fields->toJson(JSON_PRETTY_PRINT);
        }
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'fields_file' => 'required|file',
        ]);

        $content = file_get_contents($request->file('fields_file')->getRealPath());
        $data = json_decode($content, true);
        if (is_null($data)) {
            return redirect()->route('simpleWorkflow.fields.index')->with('error', 'Invalid import file.');
        }

        $fieldsData = isset($data[0]) ? $data : [$data];
        foreach ($fieldsData as $fieldData) {
            $fieldArray = [
                'id' => $fieldData['id'] ?? Str::uuid()->toString(),
                'name' => $fieldData['name'] ?? null,
                'type' => $fieldData['type'] ?? null,
                'attributes' => $fieldData['attributes'] ?? null,
            ];
            Fields::updateOrCreate(['id' => $fieldArray['id']], $fieldArray);
        }

        return redirect()->route('simpleWorkflow.fields.index')->with('success', 'Fields imported successfully.');
    }

    public static function getAll()
    {
        return Fields::orderBy('created_at')->get();
    }
    public static function getById($id)
    {
        return Fields::find($id);
    }

    public static function getByName($fieldName)
    {
        $field = Fields::where('name', $fieldName)->first();
        if ($field) {
            return $field;
        }
        return null;
    }
}

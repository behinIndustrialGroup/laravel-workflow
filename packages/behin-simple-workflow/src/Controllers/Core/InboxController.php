<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use BehinProcessMaker\Controllers\ToDoCaseController;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use App\Events\NewInboxEvent;
use App\Models\User;
use BaleBot\Controllers\BotController;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;
use Behin\SimpleWorkflow\Models\Entities\CasesManual;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Support\Str;
use Behin\SimpleWorkflow\Jobs\SendTaskReminderNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class InboxController extends Controller
{
    public static function getById($id)
    {
        return Inbox::find($id);
    }

    public function storeReminder(Request $request, string $inboxId): JsonResponse
    {
        $inbox = Inbox::with(['task', 'case'])->findOrFail($inboxId);

        if ($inbox->actor !== Auth::id()) {
            abort(403, trans('fields.You cannot set reminder for this task'));
        }

        if (!$inbox->task || !$inbox->task->show_reminder_button) {
            abort(403, trans('fields.Reminder disabled for this task'));
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'remind_at' => ['required', 'date', 'after:now'],
        ], [], [
            'title' => trans('fields.Reminder Title'),
            'message' => trans('fields.Reminder Message'),
            'remind_at' => trans('fields.Reminder At'),
        ]);

        $remindAt = Carbon::parse($data['remind_at'], config('app.timezone'));

        $caseName = $inbox->case_name ?: optional($inbox->case)->number;
        $taskName = optional($inbox->task)->name;

        SendTaskReminderNotification::dispatch(
            $inbox->id,
            (int) $inbox->actor,
            Auth::id(),
            $data['title'],
            $data['message'] ?? null,
            $caseName ? (string) $caseName : null,
            $taskName ? (string) $taskName : null,
        )->delay($remindAt);

        return response()->json([
            'status' => 200,
            'msg' => trans('fields.Reminder scheduled successfully'),
        ]);
    }

    public static function create($taskId, $caseId, $actor, $status = 'new', $caseName = null)
    {
        $task = TaskController::getById($taskId);
        if ($caseName == null)
            $createCaseName = self::createCaseName($task, $caseId);
        else
            $createCaseName = $caseName;

        $inbox = Inbox::create([
            'task_id' => $taskId,
            'case_id' => $caseId,
            'actor' => $actor,
            'status' => $status,
            // 'case_name' => $createCaseName
        ]);
        self::editCaseName($inbox->id, $createCaseName);
        $inbox->refresh();
        return $inbox;
    }

    public static function caseIsInUserInbox($caseId){
        return Inbox::where('case_id', $caseId)->whereIn('status', ['new', 'opened', 'inProgress', 'draft'])->where('actor', Auth::id())->first();
    }

    public static function editCaseName($inboxId, $caseName)
    {
        $inbox = InboxController::getById($inboxId);
        $inbox->case_name = $caseName;
        $inbox->save();
    }

    public static function getAllByTaskId($taskId): Collection
    {
        return Inbox::where('task_id', $taskId)->get();
    }

    public static function getAllByTaskIdAndCaseId($taskId, $caseId): Collection
    {
        return Inbox::where('task_id', $taskId)->where('case_id', $caseId)->get();
    }

    public static function changeStatusByInboxId($inboxId, $status)
    {
        $inboxRow = self::getById($inboxId);
        if ($inboxRow->status == 'done' and $inboxRow->actor != Auth::id()) {
            $inboxRow->status = 'doneByOther';
        } else {
            $inboxRow->status = $status;
        }
        $inboxRow->save();
    }


    public function index(Request $request): View
    {
        $rows = self::getUserInbox(Auth::id());
        $processes = $rows->pluck('task.process')->filter()->unique('id');
        if ($request->filled('process')) {
            $rows = $rows->where('task.process.id', $request->process);
        }
        return view('SimpleWorkflowView::Core.Inbox.list')->with([
            'rows' => $rows,
            'processes' => $processes,
            'selectedProcess' => $request->process
        ]);
    }

    public function categorized(Request $request)
    {
        $allRows = self::getUserInbox(Auth::id());

        $caseIds = $allRows->pluck('case_id')->filter()->unique()->values();

        $variablesCollection = $caseIds->isNotEmpty()
            ? Variable::whereIn('case_id', $caseIds)->get()
            : collect();

        $caseVariables = $variablesCollection
            ->groupBy('case_id')
            ->map(function ($group) {
                return $group->mapWithKeys(function ($variable) {
                    return [$variable->key => $variable->value];
                });
            })
            ->toArray();

        $availableVariables = $variablesCollection
            ->pluck('key')
            ->unique()
            ->sort()
            ->map(function ($key) {
                $label = trans('fields.' . $key);
                if ($label === 'fields.' . $key) {
                    $label = $key;
                }
                return [
                    'key' => $key,
                    'label' => $label,
                ];
            })
            ->values()
            ->toArray();

        $taskCategories = $allRows
            ->filter(fn($row) => $row->task)
            ->groupBy('task_id')
            ->map(function ($group) {
                $task = $group->first()->task;
                $label = trim(strip_tags($task->styled_name ?? $task->name ?? ''));
                if ($label === '') {
                    $label = trans('fields.Task Title');
                }

                return [
                    'id' => $task->id,
                    'label' => $label,
                    'count' => $group->count(),
                ];
            })
            ->sortBy(fn($category) => Str::lower($category['label']))
            ->values();

        $selectedTask = $request->get('task');
        $selectedTaskId = ($selectedTask !== null && $selectedTask !== '') ? $selectedTask : null;

        $rows = $selectedTaskId !== null
            ? $allRows->where('task_id', $selectedTaskId)->values()
            : $allRows->values();

        $selectedTaskLabel = null;
        if ($selectedTaskId !== null) {
            $selectedCategory = $taskCategories->firstWhere('id', $selectedTaskId);
            $selectedTaskLabel = $selectedCategory['label'] ?? null;
        }

        return view('SimpleWorkflowView::Core.Inbox.categorized')->with([
            'rows' => $rows,
            'taskCategories' => $taskCategories,
            'selectedTaskId' => $selectedTaskId,
            'selectedTaskLabel' => $selectedTaskLabel,
            'totalCount' => $allRows->count(),
            'availableVariables' => $availableVariables,
            'caseVariables' => $caseVariables,
        ]);
    }

    public static function getUserInbox($userId): Collection
    {
        $rows = Inbox::where('actor', $userId)
            ->whereIn('status', ['new', 'opened', 'inProgress', 'draft'])
            ->with(['task.process', 'case'])
            ->orderBy('created_at', 'desc')
            ->get();
        return $rows;
    }

    public function showCases(): View
    {
        $rows = Inbox::groupBy('case_id')->with('task')->orderBy('created_at', 'desc')->get();
        return view('SimpleWorkflowView::Core.Inbox.cases')->with([
            'rows' => $rows
        ]);
    }

    public function showInboxes($caseId): View
    {
        $rows = Inbox::where('case_id', $caseId)->with('task')->orderBy('created_at', 'desc')->get();
        return view('SimpleWorkflowView::Core.Inbox.inboxes')->with([
            'rows' => $rows
        ]);
    }

    public static function getAll(): Collection
    {
        $rows = Inbox::with('task')->orderBy('created_at', 'desc')->get();
        return $rows;
    }

    public function edit($id): View
    {
        $inbox = self::getById($id);
        return view('SimpleWorkflowView::Core.Inbox.edit')->with([
            'inbox' => $inbox
        ]);
    }

    public function update(Request $request, $id)
    {
        $inbox = self::getById($id);
        $inbox->status = $request->status;
        $inbox->actor = $request->actor;
        $inbox->case_name = $request->case_name;
        $inbox->save();
        return redirect()->back()->with([
            'success' => trans('fields.Inbox updated successfully')
        ]);
    }

    public function changeStatus($id)
    {
        $inbox = self::getById($id);
        $inbox->status = $inbox->status == 'done' ? 'new' : 'done';
        $inbox->save();
        return redirect()->back()->with([
            'success' => trans('fields.Inbox updated successfully')
        ]);
    }

    public function cancel($id)
    {
        $inbox = self::getById($id);
        $inbox->status = 'canceled';
        $inbox->save();
        Cases::where('id', $inbox->case_id)->update(['status' => 'canceled']);
        return redirect()->route('simpleWorkflow.inbox.index')->with([
            'success' => trans('fields.Case canceled successfully')
        ]);
    }

    public static function view($inboxId)
    {
        $inbox = InboxController::getById($inboxId);
        $case = CaseController::getById($inbox->case_id);
        $task = TaskController::getById($inbox->task_id);
        $process = ProcessController::getById($task->process_id);
        $form = FormController::getById($task->executive_element_id);
        $variables = VariableController::getVariablesByCaseId($case->id, $process->id);

        if ($task->type == 'form') {
            if ($task->script_before_open) {
                try {
                    $result = ScriptController::runScript($task->script_before_open, $case->id);
                    // return redirect()->route('simpleWorkflow.inbox.index')->with('error', $result);
                } catch (\Throwable $e) {
                    Log::error('script_before_open failed: ' . $e->getMessage());
                }
            }
            if (!isset($form->content)) {
                return redirect()->route('simpleWorkflow.inbox.index')->with('error', trans('Form not found'));
            }
            if($task->assignment_type == 'public'){
                return view('SimpleWorkflowView::Core.Inbox.public-show')->with([
                    'inbox' => $inbox,
                    'case' => $case,
                    'task' => $task,
                    'process' => $process,
                    'variables' => $variables,
                    'form' => $form
                ]);
            }
            if($inbox->actor != Auth::id()){
                return abort(403, trans("fields.Sorry you don't have permission to see this page"));
            }
            if($inbox->status == 'new'){
                $inbox->status = 'opened';
                $inbox->save();
            }
            return view('SimpleWorkflowView::Core.Inbox.show')->with([
                'inbox' => $inbox,
                'case' => $case,
                'task' => $task,
                'process' => $process,
                'variables' => $variables,
                'form' => $form
            ]);
        }
    }

    public function delete(Request $request, $id)
    {
        $inbox = self::getById($id);
        // if($inbox->status == 'draft'){
        $inbox->delete();
        return redirect()->route('simpleWorkflow.inbox.index')->with('success', trans('fields.Inbox deleted successfully'));
        // }

    }

    public static function createCaseName(Task $task, $caseId)
    {
        // دریافت متغیرها از جدول variables
        $variables = VariableController::getVariablesByCaseId($caseId)
            ->pluck('value', 'key')
            ->toArray();
        // دریافت عنوان تسک
        $title = $task->case_name;

        if (!$task->case_name) {
            if(class_exists(CasesManual::class)){
                $case = CasesManual::find($caseId);
                return $case->createName();
            }

            // if (method_exists($case, 'name')) {
            //     $case_name = $case->name();
            //     if ($case_name) {
            //         return $case_name;
            //     }
            // }
        }

        // جایگزینی متغیرها در عنوان
        $patterns = config('workflow.patterns');
        // Log::info(json_encode($patterns));


        $replacements = [];
        foreach ($patterns as $key) {
            // $title = str_replace('@' . $key, $variables[$key] ?? 'پیدا نشد', $title);
            $replacements[] = $variables[$key] ?? 'پیدا نشد';
        }
        // return $replacements;

        $p = [];
        foreach ($patterns as $key) {
            $p[] = '/@' . $key . '/i';
        }

        $title = preg_replace($p, $replacements, $title);
        return $title;
    }

    public static function caseHistory($caseNumber){
        $cases = CaseController::getAllByCaseNumber($caseNumber)->pluck('id');
        $rows= Inbox::whereIn('case_id', $cases)->orderBy('created_at')->get();
        return view('SimpleWorkflowView::Core.Inbox.history', compact('rows'));
    }

    public static function caseHistoryList($caseNumber, $limit = null){
        $cases = CaseController::getAllByCaseNumber($caseNumber)->pluck('id');
        $rows= Inbox::whereIn('case_id', $cases)->orderBy('created_at');
        if($limit)
            return $rows->limit($limit)->get();
        return $rows->get();
    }

    public static function caseHistoryListBefore($caseNumber, $inboxId, $limit = null){
        $cases = CaseController::getAllByCaseNumber($caseNumber)->pluck('id');
        $inbox = InboxController::getById($inboxId);
        $rows= Inbox::whereIn('case_id', $cases)->orderBy('created_at','desc')->whereNot('id', $inboxId)->where('created_at', '<=', $inbox->created_at);
        if($limit)
            return $rows->limit($limit)->get();
        return $rows->get();
    }
}


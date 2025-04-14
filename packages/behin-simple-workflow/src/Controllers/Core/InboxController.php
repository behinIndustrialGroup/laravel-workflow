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
use App\Events\NewInboxEvent;
use App\Models\User;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;

class InboxController extends Controller
{
    public static function getById($id): Inbox
    {
        return Inbox::find($id);
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

    public function index(): View
    {
        $rows = self::getUserInbox(Auth::id());
        return view('SimpleWorkflowView::Core.Inbox.list')->with([
            'rows' => $rows
        ]);
    }

    public static function getUserInbox($userId): Collection
    {
        $rows = Inbox::where('actor', $userId)->whereIn('status', ['new', 'opened', 'inProgress', 'draft'])->with('task')->orderBy('created_at', 'desc')->get();
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

    public static function view($inboxId)
    {
        $inbox = InboxController::getById($inboxId);
        $case = CaseController::getById($inbox->case_id);
        $task = TaskController::getById($inbox->task_id);
        $process = ProcessController::getById($task->process_id);
        $form = FormController::getById($task->executive_element_id);
        $variables = VariableController::getVariablesByCaseId($case->id, $process->id);
        
        if ($task->type == 'form') {
            if (!isset($form->content)) {
                return redirect()->route('simpleWorkflow.inbox.index')->with('error', trans('Form not found'));
            }
            // if(!TaskActorController::userIsAssignToTask($task->id, Auth::id())){
            //     return redirect()->route('simpleWorkflow.inbox.index')->with('error', trans('You are not assigned to this task'));
            // }
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
}

<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class InboxController extends Controller
{
    public static function getById($id): Inbox
    {
        return Inbox::find($id);
    }

    public static function create($taskId, $caseId, $actor, $status = 'new')
    {
        $task = TaskController::getById($taskId);
        $createCaseName = self::createCaseName($task, $caseId);


        return Inbox::create([
            'task_id' => $taskId,
            'case_id' => $caseId,
            'actor' => $actor,
            'status' => $status,
            'case_name' => $createCaseName
        ]);
    }

    public static function changeStatusByInboxId($inboxId, $status)
    {
        $inboxRow = self::getById($inboxId);
        $inboxRow->status = $status;
        $inboxRow->save();
    }

    public function index(): View
    {
        return view('SimpleWorkflowView::Core.Inbox.list')->with([
            'rows' => self::getUserInbox(Auth::id())
        ]);
    }

    public static function getUserInbox($userId): Collection
    {
        $rows = Inbox::where('actor', $userId)->whereIn('status', ['new', 'opened', 'inProgress'])->with('task')->orderBy('created_at', 'desc')->get();
        return $rows;
    }

    public static function view($inboxId)
    {
        $inbox = InboxController::getById($inboxId);
        $case = CaseController::getById($inbox->case_id);
        $task = TaskController::getById($inbox->task_id);
        $process = ProcessController::getById($task->process_id);
        $form = FormController::getById($task->executive_element_id);
        $variables = VariableController::getVariablesByCaseId($case->id);
        if ($task->type == 'form') {
            if (!isset($form->content)) {
                return redirect()->route('simpleWorkflow.inbox.index')->with('error', trans('Form not found'));
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

    public static function createCaseName(Task $task, $caseId)
    {
        // دریافت متغیرها از جدول variables
        $variables = VariableController::getVariablesByCaseId($caseId)
            ->pluck('value', 'key')
            ->toArray();

        // دریافت عنوان تسک
        $title = $task->case_name;

        // جایگزینی متغیرها در عنوان
        $patterns = [
            '/@customer_name/',
            '/@customer_city/',
            '/@customer_mobile/'
        ];

        $replacements = [
            $variables['customer_name'] ?? '-',
            $variables['customer_city'] ?? '-',
            $variables['customer_mobile'] ?? '-'
        ];

        $title = preg_replace($patterns, $replacements, $title);
        return $title;
    }
}

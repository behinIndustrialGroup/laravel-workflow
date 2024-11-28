<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoutingController extends Controller
{
    public static function save(Request $request)
    {
        $request->validate([
            'processId' => 'required',
            'taskId' => 'required',
            'caseId' => 'required',
        ]);

        $processId = $request->processId;
        $taskId = $request->taskId;
        $caseId = $request->caseId;

        $vars = $request->all();
        foreach ($vars as $key => $value) {
            VariableController::save($processId, $caseId, $key, $value);
        }
        return response()->json(
            [
                'msg' => trans('Saved')
            ]
        );
    }

    public static function saveAndNext(Request $request)
    {
        self::save($request);
        $caseId = $request->caseId;
        $processId = $request->processId;
        $taskId = $request->taskId;
        $inbox = InboxController::getById($request->inboxId);
        $task = $inbox->task;
        $taskChildren = $task->children();
        // return $taskChildren;
        if ($task->type == 'form') {
            if ($task->assignment_type == 'normal') {
                InboxController::changeStatusByInboxId($request->inboxId, 'done');
                //از این رکورد در اینباکس یک ردیف وجود دارد
                // وضعیت همین رکورد باید در اینباکس به انجام شده تغییر کند
            }
            if ($task->assignment_type == 'dynamic') {
                InboxController::changeStatusByInboxId($request->inboxId, 'done');
                //از این رکورد در اینباکس یک ردیف وجود دارد
                // وضعیت همین رکورد باید در اینباکس به انجام شده تغییر کند
            }
            if ($task->assignment_type == 'parallel') {
                // از این رکورد چند ردیف در اینباکس وجود دارد
                // همه باید وضعیت انجام شده تغییر کنند
            }
        }
        foreach ($taskChildren as $task) {
            $result = self::executeNextTask($task, $caseId);
            if($result == 'break'){
                break;
            }
        }
    }

    public static function executeNextTask($task, $caseId)
    {
        if ($task->type == 'form') {
            if ($task->assignment_type == 'normal' or $task->assignment_type == null) {
                $taskActors = TaskActorController::getActorsByTaskId($task->id)->pluck('actor');
                foreach ($taskActors as $actor) {
                    InboxController::create($task->id, $caseId, $actor, 'new');
                }
                echo json_encode($taskActors);
            }
            if ($task->assignment_type == 'dynamic') {
                $taskActors = TaskActorController::getDynamicTaskActors($task->id, $caseId)->pluck('actor');
                foreach ($taskActors as $actor) {
                    InboxController::create($task->id, $caseId, $actor, 'new');
                }
            }
            if ($task->assignment_type == 'parallel') {
                // مشابه نورمال
            }
        }
        if ($task->type == 'script') {
            $script = ScriptController::getById($task->executive_element_id);
            ScriptController::runScript($task->executive_element_id, $caseId);
            $taskChildren = $task->children();
            foreach ($taskChildren as $task) {
                self::executeNextTask($task, $caseId);
            }
        }
        if ($task->type == 'condition') {
            $condition = ConditionController::getById($task->executive_element_id);
            $result = ConditionController::runCondition($task->executive_element_id, $caseId);
            print($result);

            if ($result) {
                $nextTask = $condition->nextIfTrue();
                if((bool)$nextTask){
                    self::executeNextTask($nextTask, $caseId);
                }else{
                    $taskChildren = $task->children();
                    foreach ($taskChildren as $task) {
                        print($task->name);
                        self::executeNextTask($task, $caseId);
                    }
                }

                return 'break';
            }
        }
    }
}

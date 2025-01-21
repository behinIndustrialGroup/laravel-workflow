<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use BaleBot\BaleBotProvider;
use BaleBot\Controllers\BotController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoutingController extends Controller
{
    public static function createCaseNumberAndSave(Request $request)
    {
        $request->validate([
            'processId' => 'required',
            'caseId' => 'required',
            'inboxId' => 'required',
        ]);
        $processId = $request->processId;
        $caseId = $request->caseId;
        $inboxId = $request->inboxId;

        $vars = $request->all();
        $newCaseNumber = CaseController::getNewCaseNumber($processId);
        CaseController::setCaseNumber($caseId, $newCaseNumber);
        InboxController::changeStatusByInboxId($inboxId, 'new');
        return self::save($request);
    }
    public static function save(Request $request, $requiredFields = [])
    {
        $request->validate([
            'processId' => 'required',
            'caseId' => 'required',
        ]);
        $processId = $request->processId;
        $caseId = $request->caseId;

        $vars = $request->all();

        foreach ($vars as $key => $value) {
            if (gettype($value) == 'object') {
                VariableController::saveFile($processId, $caseId, $key, $value);
            } else {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                VariableController::save($processId, $caseId, $key, $value);
            }
        }
        foreach ($requiredFields as $field) {
            $var = VariableController::getVariable($processId, $caseId, $field);
            if (!$var?->value) {
                return
                    [
                        'status' => 400,
                        'msg' => trans('fields.' . $field) . ': ' . trans('fields.Required')
                    ];
            }
        }
        return
            [
                'status' => 200,
                'msg' => trans('Saved')
            ];
    }

    public static function saveAndNext(Request $request)
    {
        $caseId = $request->caseId;
        $processId = $request->processId;
        $taskId = $request->taskId;
        $process = ProcessController::getById($processId);
        $inbox = InboxController::getById($request->inboxId);

        if(in_array($inbox->status , ['done', 'doneByOther'])){
            return response()->json([
               'status' => 400,
               'msg' => trans('fields.Task Has Been Done Previously')
            ]);
        }

        $task = $inbox->task;
        $form = $task->executiveElement();
        $requiredFields = FormController::requiredFields($form->id);
        $result = self::save($request, $requiredFields);
        if ($result['status'] != 200) {
            return $result;
        }
        if ($process->number_of_error) {
            return response()->json([
                'status' => 400,
                'msg' => trans('fields.Process Has Error')
            ]);
        }

        $taskChildren = $task->children();

        if ($task->next_element_id) {
            $nextTask = TaskController::getById($task->next_element_id);
            $result = self::executeNextTask($nextTask, $caseId);
            if ($result) {
                return $result;
            }
        } else {
            foreach ($taskChildren as $childTask) {
                $result = self::executeNextTask($childTask, $caseId);
                if($result == 'break'){
                    break;
                }
                if ($result) {
                    return $result;
                }
            }
        }
        if ($task->type == 'form') {
            if ($task->assignment_type == 'normal') {
                $inboxes = InboxController::getAllByTaskIdAndCaseId($task->id, $caseId);
                foreach ($inboxes as $inbox) {
                    InboxController::changeStatusByInboxId($inbox->id, 'done');
                }
                // InboxController::changeStatusByInboxId($request->inboxId, 'done');
                //از این رکورد در اینباکس یک یا چمد ردیف وجود دارد
                // وضعیت همه رکوردها باید در اینباکس به انجام شده تغییر کند
            }
            if ($task->assignment_type == 'dynamic') {
                InboxController::changeStatusByInboxId($request->inboxId, 'done');
                //از این رکورد در اینباکس یک ردیف وجود دارد
                // وضعیت همین رکورد باید در اینباکس به انجام شده تغییر کند
            }
            if ($task->assignment_type == 'parallel') {
                InboxController::changeStatusByInboxId($inbox->id, 'done');
                // از این رکورد چند ردیف در اینباکس وجود دارد
                // همه باید وضعیت انجام شده تغییر کنند
            }
        }
        return response()->json([
            'status' => 200,
            'msg' => trans('Saved')
        ]);
    }

    public static function executeNextTask($task, $caseId)
    {
        try {
            if ($task->type == 'form') {
                if ($task->assignment_type == 'normal' or $task->assignment_type == null) {
                    $taskActors = TaskActorController::getActorsByTaskId($task->id)->pluck('actor');
                    foreach ($taskActors as $actor) {
                        InboxController::create($task->id, $caseId, $actor, 'new');
                    }
                    // echo json_encode($taskActors);
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
                $result = ScriptController::runScript($task->executive_element_id, $caseId);
                if ($result) {
                    return response()->json([
                        'status' => 400,
                        'msg' => $result
                    ]);
                }
                if ($task->next_element_id) {
                    $nextTask = TaskController::getById($task->next_element_id);
                    self::executeNextTask($nextTask, $caseId);
                }
                $taskChildren = $task->children();
                foreach ($taskChildren as $task) {
                    self::executeNextTask($task, $caseId);
                }
            }
            if ($task->type == 'condition') {
                $condition = ConditionController::getById($task->executive_element_id);
                $result = ConditionController::runCondition($task->executive_element_id, $caseId);
                // print($result);

                if ($result) {
                    $nextTask = $condition->nextIfTrue();
                    if ((bool)$nextTask) {
                        self::executeNextTask($nextTask, $caseId);
                    } else {
                        if ($task->next_element_id) {
                            $nextTask = TaskController::getById($task->next_element_id);
                            self::executeNextTask($nextTask, $caseId);
                        }
                        $taskChildren = $task->children();
                        foreach ($taskChildren as $task) {
                            // print($task->name);
                            self::executeNextTask($task, $caseId);
                        }
                    }

                    return 'break';
                }
            }
        } catch (Exception $th) {
            // BotController::sendMessage(681208098, $th->getMessage());
            return response()->json(['status' => 400, 'msg' => $th->getMessage()]);
        }
    }
}

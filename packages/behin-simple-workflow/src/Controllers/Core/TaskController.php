<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index($process_id)
    {
        $process = ProcessController::getById($process_id);
        $forms = FormController::getAll();
        $scripts = ScriptController::getAll();
        $conditions = ConditionController::getAll();
        return view('SimpleWorkflowView::Core.Task.create')->with([
            'process' => $process,
            'forms' => $forms,
            'scripts'=> $scripts,
            'conditions'=> $conditions,
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $data['is_preview'] = true;
        $data['show_save_button'] = $request->boolean('show_save_button');
        $data['show_reminder_button'] = $request->boolean('show_reminder_button');
        $data['show_cancel_button'] = $request->has('show_cancel_button')
            ? $request->boolean('show_cancel_button')
            : true;
        $task = Task::create($data);
        if (!$request->parent_id) {
            $task->parent_id = $task->id;
            $task->save();
        }
        ProcessController::processHasError($task->process_id);
        return redirect(route('simpleWorkflow.task.index', ['process_id'=> $task->process_id]));
    }

    public function edit(Task $task)
    {
        return view('SimpleWorkflowView::Core.Task.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->only('name', 'executive_element_id', 'parent_id', 'next_element_id', 'assignment_type', 'case_name', 'color', 'background', 'duration', 'order', 'timing_type', 'timing_value', 'timing_key_name', 'number_of_task_to_back', 'script_before_open', 'allow_cancel', 'is_preview', 'show_save_button', 'show_reminder_button', 'show_cancel_button');
        $data['is_preview'] = $request->boolean('is_preview');
        $data['show_save_button'] = $request->boolean('show_save_button');
        $data['show_reminder_button'] = $request->boolean('show_reminder_button');
        $data['show_cancel_button'] = $request->boolean('show_cancel_button');
        $task->update($data);
        // self::getById($request->id)->update($request->all());
        return redirect()->back()->with('success', trans('Updated Successfully'));
    }

    public function destroy(Request $request, Task $task)
    {
        $request->validate([
            'transfer_task_id' => 'required|exists:wf_task,id',
        ]);

        $transferTaskId = $request->transfer_task_id;

        $inboxes = InboxController::getAllByTaskId($task->id);
        foreach ($inboxes as $inbox) {
            $inbox->task_id = $transferTaskId;
            $newTask = self::getById($transferTaskId);
            $caseName = InboxController::createCaseName($newTask, $inbox->case_id);
            InboxController::editCaseName($inbox->id, $caseName);
            $inbox->save();
        }

        $processId = $task->process_id;
        $task->delete();

        return redirect()->route('simpleWorkflow.task.index', ['process_id' => $processId])
            ->with('success', trans('fields.Task deleted successfully'));
    }

    public static function getById($id){
        return Task::find($id);
    }

    public static function getAll(){
        return Task::get();
    }

    public static function getProcessTasks($process_id, $includePreview = true)
    {
        $query = Task::where('process_id', $process_id);
        if (!$includePreview) {
            $query->where(function ($subQuery) {
                $subQuery->where('is_preview', false)
                    ->orWhereNull('is_preview');
            });
        }

        return $query->get();
    }

    public static function getProcessStartTasks($process_id, $includePreview = true)
    {
        $query = Task::where('process_id', $process_id)->whereColumn('id', 'parent_id');

        if (!$includePreview) {
            $query->where(function ($subQuery) {
                $subQuery->where('is_preview', false)
                    ->orWhereNull('is_preview');
            });
        }

        return $query->get();
    }

    public static function TaskHasError($taskId){
        $task = TaskController::getById($taskId);
        if (!$task) {
            return false;
        }
        if ($task->is_preview) {
            return false;
        }
        $hasError = 0;
        if($task->type == 'form'){
            // $hasError++;
            if($task->actors()->count() == 0 and $task->assignment_type != 'public'){
                $hasError++;
                $descriptions = trans('fields.don\'t have actor');
            }
            if($task->assignment_type == null){
                $hasError++;
                $descriptions = trans('fields.don\'t have assignment type');
            }
        }
        if($task->type == 'condition'){
            if($task->executive_element_id == null){
                $hasError++;
                $descriptions = trans('fields.don\'t have executive element');
            }
        }
        if($task->type == 'script'){
            if($task->executive_element_id == null){
                $hasError++;
                $descriptions = trans('fields.don\'t have executive element');
            }
        }
        if($hasError > 0){
            return [
                'hasError' => $hasError,
                'descriptions' => $descriptions,
            ];
        }
        return false;
    }

}

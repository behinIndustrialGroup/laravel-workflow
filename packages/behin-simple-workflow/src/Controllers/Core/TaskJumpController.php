<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskJump;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class TaskJumpController extends Controller
{
    public function getTaskJumps($task_id)
    {
        return TaskJump::where('task_id', $task_id)->get();
    }

    public function show($task_id , $inbox_id , $case_id , $process_id)
    {
        $task = TaskController::getById($task_id);
        $case = CaseController::getById($case_id);
        if($task->number_of_task_to_back > 0){
            $previousInboxes = InboxController::caseHistoryListBefore($case->number, $inbox_id, $task->number_of_task_to_back);
            return view('SimpleWorkflowView::Core.TaskJump.jump-modal', compact('task' , 'inbox_id' , 'case_id' , 'process_id', 'previousInboxes'));
        }
        return view('SimpleWorkflowView::Core.TaskJump.jump-modal', compact('task' , 'inbox_id' , 'case_id' , 'process_id'));
    }

    public function store(Request $request)
    {
        TaskJump::create([
            'task_id' => $request->task_id,
            'next_task_id' => $request->next_task_id,
        ]);
        return redirect()->back()->with('success', trans('fields.Added'));
    }

    public function update(Request $request, $id)
    {
        $jump = TaskJump::findOrFail($id);
        if($request->delete == 1){
            $jump->delete();
            return redirect()->back()->with('success', trans('fields.Deleted'));
        }
        $jump->update([
            'task_id' => $request->task_id,
            'next_task_id' => $request->next_task_id,
        ]);
        return redirect()->back()->with('success', trans('fields.Updated'));
    }

    public function destroy($id)
    {
        TaskJump::destroy($id);
        return redirect()->back()->with('success', trans('fields.Deleted'));
    }

}

<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskActorController extends Controller
{
    public static function userIsAssignToTask($taskId, $userId)
    {
        return TaskActor::where('task_id', $taskId)->where('actor', $userId)->first();
    }

    public static function getActorsByTaskId($taskId)
    {
        return TaskActor::where('task_id', $taskId)->get();
    }

    public static function getDynamicTaskActors($taskId, $caseId)
    {
        $case = CaseController::getById($caseId);
        $variables = collect($case->variables());
        return self::getActorsByTaskId($taskId)->each(function($row)use($variables){
            $user = $variables->where('key', $row->actor)->first()?->value;
            if($user){
                $row->actor = $user;
            }else{
                $row->actor = 1;
            }
        });
        return TaskActor::where('task_id', $taskId)->get();
    }

    public static function getAll()
    {
        return TaskActor::with(['task', 'actor'])->get();
    }

    public function index()
    {
        $taskActors = self::getAll();
        return view('SimpleWorkflowView::Core.TaskActor.index', compact('taskActors'));
    }

    public function store(Request $request)
    {
        TaskActor::create($request->only('task_id', 'actor'));
        return redirect()->back();
    }

    public function destroy(TaskActor $taskActor)
    {
        $taskActor->delete();
        return redirect()->back();
    }
}

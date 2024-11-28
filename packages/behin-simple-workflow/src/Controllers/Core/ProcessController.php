<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessController extends Controller
{
    public function index(): View
    {
        $processes = self::getAll();
        return view('SimpleWorkflowView::Core.Process.index', compact('processes'));
    }

    public function create(): View
    {
        return view('SimpleWorkflowView::Core.Process.create');
    }

    public function store(Request $request): Process
    {
        return Process::create($request->all());
    }

    public static function getById($id): Process
    {
        return Process::find($id);
    }

    public static function getAll(): object
    {
        return Process::get();
    }

    public static function listOfProcessThatUserCanStart($userId = null):array
    {
        $userId = $userId ? $userId : Auth::id();
        $processes = self::getAll();
        $ar = [];
        foreach($processes as $process)
        {
            $startTasks = TaskController::getProcessStartTasks($process->id);
            foreach($startTasks as $startTask)
            {
                $result = TaskActorController::userIsAssignToTask($startTask->id, $userId);
                if($result)
                {
                    $process->task = $startTask;
                    $ar[] = $process;
                }
            }
        }

        return $ar;
    }

    public static function startListView():View
    {
        return view('SimpleWorkflowView::Core.Process.start-list')->with([
            'processes' => self::listOfProcessThatUserCanStart()
        ]);
    }

    public static function start($taskId)
    {
        $task = TaskController::getById($taskId);
        $listOfProcessThatUserCanStart = collect(self::listOfProcessThatUserCanStart(Auth::id()))->pluck('id')->toArray();
        if(!in_array($task->process_id, $listOfProcessThatUserCanStart))
        {
            return response()->json([
                'msg' => trans("You don't have permission to start this process")
            ], 403);
        }
        $case = CaseController::create($task->process_id, Auth::id() );
        InboxController::create($taskId, $case->id, Auth::id(), 'new');
        //craete case in user inbox
    }
}

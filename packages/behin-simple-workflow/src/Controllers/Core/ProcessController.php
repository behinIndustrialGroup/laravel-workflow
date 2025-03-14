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

    public static function start($taskId, $force = false, $redirect = true, $inDraft = false, $caseNumber = null, $creator = null, $parentId = null)
    {
        $task = TaskController::getById($taskId);
        if(!$force)
        {
            $listOfProcessThatUserCanStart = collect(self::listOfProcessThatUserCanStart(Auth::id()))->pluck('id')->toArray();
            if(!in_array($task->process_id, $listOfProcessThatUserCanStart))
            {
            return response()->json([
                    'msg' => trans("You don't have permission to start this process")
                ], 403);
            }
        }
        $creator = $creator ? $creator : Auth::user()->id;
        $case = CaseController::create($task->process_id, $creator, null, $inDraft, $caseNumber, $parentId);
        $status = $inDraft ? 'draft' : 'new';
        $inbox = InboxController::create($taskId, $case->id, $creator, $status);
        if($redirect)
        {
            // return InboxController::view($inbox->id);
            return redirect()->route('simpleWorkflow.inbox.view', $inbox->id);
        }
        return $inbox;
    }

    public static function processHasError($processId){
        $process = ProcessController::getById($processId);
        $hasError = 0;
        foreach($process->tasks() as $task){
            if(TaskController::TaskHasError($task->id)){
                $hasError++;
            }
        }
        $process->number_of_error =  $hasError;
        $process->save();
        return $hasError;
    }

    public static function startFromScript($taskId, $creator, $caseNumber = null, $parentId){
        return self::start($taskId, true, false, false, $caseNumber, $creator, $parentId);
    }
}

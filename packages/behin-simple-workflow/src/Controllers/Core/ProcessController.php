<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskJump;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Script;
use Behin\SimpleWorkflow\Models\Core\Condition;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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

    public function edit($processId): View
    {
        $process = Process::findOrFail($processId);
        return view('SimpleWorkflowView::Core.Process.edit', compact('process'));
    }

    public function update(Request $request, $processId)
    {
        $process = Process::findOrFail($processId);
        $process->update($request->only(['name', 'category', 'case_prefix']));
        return redirect()->route('simpleWorkflow.process.index');
    }

    public static function getById($id): Process
    {
        return Process::find($id);
    }

    public static function getAll(): object
    {
        return Process::orderBy('created_at','desc')->get();
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

    public function exportView($processId): View
    {
        $process = Process::findOrFail($processId);
        $data = $process->toArray();
        $data['tasks'] = [];

        foreach ($process->tasks() as $task) {
            $taskArr = $task->toArray();
            $taskArr['actors'] = $task->actors()->get()->toArray();
            $taskArr['jumps'] = $task->jumps()->get()->toArray();

            $executive = $task->executiveElement();
            if ($executive) {
                switch ($task->type) {
                    case 'form':
                    case 'script':
                        $taskArr['executive'] = Arr::only($executive->toArray(), ['id', 'name', 'executive_file', 'content']);
                        break;
                    case 'condition':
                        $taskArr['executive'] = Arr::only($executive->toArray(), ['id', 'name', 'content', 'next_if_true']);
                        break;
                }
            }

            $data['tasks'][] = $taskArr;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return view('SimpleWorkflowView::Core.Process.export', compact('json'));
    }

    public function importView(): View
    {
        return view('SimpleWorkflowView::Core.Process.import');
    }
    /**
     * Export the complete definition of a process including its tasks,
     * actors and jumps. The result is returned as JSON so it can be
     * imported in another instance of the application.
     */
    public function export($processId)
    {
        $process = Process::findOrFail($processId);
        $data = $process->toArray();
        $data['tasks'] = [];

        foreach ($process->tasks() as $task) {
            $taskArr = $task->toArray();
            $taskArr['actors'] = $task->actors()->get()->toArray();
            $taskArr['jumps'] = $task->jumps()->get()->toArray();
            $executive = $task->executiveElement();
            if ($executive) {
                switch ($task->type) {
                    case 'form':
                    case 'script':
                        $taskArr['executive'] = Arr::only($executive->toArray(), ['id', 'name', 'executive_file', 'content']);
                        break;
                    case 'condition':
                        $taskArr['executive'] = Arr::only($executive->toArray(), ['id', 'name', 'content', 'next_if_true']);
                        break;
                }
            }
            $data['tasks'][] = $taskArr;
        }

        return response()->json($data);
    }

    /**
     * Import a process definition previously exported by the export
     * method. All tasks and their relations are recreated while the
     * new identifiers are mapped so relationships stay intact.
     */
    public function import(Request $request)
    {
        $payload = $request->json()->all();
        $process = null;

        DB::transaction(function () use ($payload, &$process) {
            $processData = Arr::except($payload, ['tasks']);
            $process = Process::create($processData);

            $tasksMap = [];

            foreach ($payload['tasks'] ?? [] as $task) {
                $oldId = $task['id'] ?? null;
                $actors = $task['actors'] ?? [];
                $jumps = $task['jumps'] ?? [];
                $parentOld = $task['parent_id'] ?? null;
                $nextOld = $task['next_element_id'] ?? null;
                $executive = $task['executive'] ?? null;

                $taskData = Arr::except($task, ['id', 'actors', 'jumps', 'parent_id', 'next_element_id', 'executive']);
                $taskData['process_id'] = $process->id;
                $taskData['parent_id'] = null;
                $taskData['next_element_id'] = null;

                $newTask = Task::create($taskData);

                $tasksMap[$oldId] = [
                    'model' => $newTask,
                    'parent_old' => $parentOld,
                    'next_old' => $nextOld,
                    'jumps' => $jumps,
                    'condition' => null,
                    'condition_next_old' => null,
                ];

                foreach ($actors as $actor) {
                    TaskActor::create([
                        'task_id' => $newTask->id,
                        'actor' => $actor['actor'] ?? null,
                    ]);
                }
                if ($executive) {
                    switch ($task['type'] ?? null) {
                        case 'form':
                            $formData = Arr::only($executive, ['name', 'executive_file', 'content']);
                            $form = null;
                            if (!empty($executive['id']) && ($existing = Form::find($executive['id']))) {
                                $existing->update($formData);
                                $form = $existing;
                            } else {
                                $form = Form::create($formData);
                            }
                            $newTask->executive_element_id = $form->id;
                            $newTask->save();
                            break;
                        case 'script':
                            $scriptData = Arr::only($executive, ['name', 'executive_file', 'content']);
                            $script = null;
                            if (!empty($executive['id']) && ($existing = Script::find($executive['id']))) {
                                $existing->update($scriptData);
                                $script = $existing;
                            } else {
                                $script = Script::create($scriptData);
                            }
                            $newTask->executive_element_id = $script->id;
                            $newTask->save();
                            break;
                        case 'condition':
                            $nextIfTrueOld = $executive['next_if_true'] ?? null;
                            $condData = Arr::only($executive, ['name', 'content']);
                            $condData['next_if_true'] = null;
                            $condition = null;
                            if (!empty($executive['id']) && ($existing = Condition::find($executive['id']))) {
                                $existing->update($condData);
                                $condition = $existing;
                            } else {
                                $condition = Condition::create($condData);
                            }
                            $newTask->executive_element_id = $condition->id;
                            $newTask->save();
                            $tasksMap[$oldId]['condition'] = $condition;
                            $tasksMap[$oldId]['condition_next_old'] = $nextIfTrueOld;
                            break;
                    }
                }
            }

            foreach ($tasksMap as $oldId => $entry) {
                $task = $entry['model'];

                if ($entry['parent_old'] && isset($tasksMap[$entry['parent_old']])) {
                    $task->parent_id = $tasksMap[$entry['parent_old']]['model']->id;
                }

                if ($entry['next_old'] && isset($tasksMap[$entry['next_old']])) {
                    $task->next_element_id = $tasksMap[$entry['next_old']]['model']->id;
                }

                $task->save();

                foreach ($entry['jumps'] as $jump) {
                    $next = $jump['next_task_id'] ?? null;
                    if ($next && isset($tasksMap[$next])) {
                        TaskJump::create([
                            'task_id' => $task->id,
                            'next_task_id' => $tasksMap[$next]['model']->id,
                        ]);
                    }
                }
                if ($entry['condition'] && $entry['condition_next_old'] && isset($tasksMap[$entry['condition_next_old']])) {
                    $entry['condition']->next_if_true = $tasksMap[$entry['condition_next_old']]['model']->id;
                    $entry['condition']->save();
                }
            }
        });

        return response()->json(['status' => 'ok', 'process_id' => $process->id]);
    }
}

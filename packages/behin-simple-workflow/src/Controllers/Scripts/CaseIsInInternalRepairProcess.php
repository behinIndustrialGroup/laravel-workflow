<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class CaseIsInInternalRepairProcess extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = $case;
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $requestedCaseNumber = $case->getVariable('case_number');
        $cases = Cases::where('number', $requestedCaseNumber)->pluck('id');
        $currentTasks = Inbox::where('status', 'new')->whereIn('case_id', $cases)->pluck('task_id');
        foreach($currentTasks as $taskId){
            $processId = Task::find($taskId)->process_id;
            if(
                $processId != "" or 
                in_array($processId, [
                    ""
                    ])
                ){
                    return 'false';
                }
        }
    }
}
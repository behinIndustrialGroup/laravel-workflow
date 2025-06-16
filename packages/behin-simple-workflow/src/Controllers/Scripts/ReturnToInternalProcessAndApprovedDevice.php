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
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Parts;

class ReturnToInternalProcessAndApprovedDevice extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        
        $parentCaseId = $case->parent_id;
        $parentCase = CaseController::getById($parentCaseId);
        $parts = Parts::where('case_number', $case->number)->get();
        foreach($parts as $part){
            if($part->dispatched_expert_needed == 'بله'){
                $parentCase->saveVariable('dispatched_expert_needed', 'بله');
                $parentCase->saveVariable('dispatched_expert_description', 
                    $parentCase->getVariable('dispatched_expert_description') . ' - ' . $part->dispatched_expert_description
                );
            }
        }
        $mapaExpertHead = $case->getVariable('mapa_expert_head_for_internal_process');
        
        //مرحله تعیین هزینه تعمیرات از فرایند داخلی
        $task = TaskController::getById("19a1be98-7b4a-4100-903d-e6612c4c4a6c");
        
        //ایجاد رکورد جدید جهت تایید دستگاه
        $inbox = InboxController::create(
            $task->id, 
            $parentCaseId,
            38,
            'new',
            // case name
        );
        
 
    }
}
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

class SendMapaCenterCaseForOnAccountPayment extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        // $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request)
    {
        $caseId = $request->caseId;
        $case = CaseController::getById($caseId);
        
        //مرحله تعیین هزینه فرایند مپا سنتر
        $task = TaskController::getById("062b5000-07c2-435c-bb45-621ed15cb42c");
        
        // $inbox = Inbox::where('case_id', $case->id)->where('task_id', $task->id)->first();
        // if($inbox){
        //     return response("این دستگاه قبلا برای تعیین هزینه ارسال شده است", 402);
        // }
        
        //ایجاد رکورد جدید جهت تایید دستگاه
        $inbox = ProcessController::startFromScript(
            $task->id, 
            38,
            $case->number,
            $case->id
        );
            
        InboxController::editCaseName(
            $inbox->id,
            $case->getVariable('customer_workshop_or_ceo_name') . " | دستگاه: ". $case->getVariable('device_name')
            );
        $newCaseId = $inbox->case_id;
        $newCase = CaseController::getById($newCaseId);
        $newCase->copyVariableFrom($case->id);
        $newCase->saveVariable('repair_cost_description', 'هزینه علی الحساب دریافت شود ');
        
 
    }
}
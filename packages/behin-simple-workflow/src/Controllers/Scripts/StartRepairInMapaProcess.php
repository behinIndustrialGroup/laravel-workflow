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

class StartRepairInMapaProcess extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        $previousVariables = $this->case->variables();
        $task = TaskController::getById("9f6b7b5c-155e-4698-8b05-26ebb061bb7d");
        
        //شروه فرایند جدید پذیرش در مدارپرداز
        $inbox = ProcessController::startFromScript(
            $task->id, 42, $case->number,
            $this->case->id
        );
        
        //ویرایش نام پرونده در ردیف کارتابل کارشناس
        $inbox->case_name = "پذیرش دستگاه ";
        $inbox->case_name .= $case->getVariable('exited_device_name');
        $inbox->case_name .= " از فرایند خارجی";
        $inbox->save();
        
        $newCaseId = $inbox->case_id;
        $newCase = CaseController::getById($newCaseId);
        
        $newCase->saveVariable('customer_workshop_or_ceo_name', $case->getVariable('customer_workshop_or_ceo_name'));
        
        $newCase->saveVariable('customer_mobile', $case->getVariable('customer_mobile'));
        
        $newCase->saveVariable('device_name', $case->getVariable('exited_device_name'));
        
        $newCase->saveVariable('device_model', $case->getVariable('exited_device_model'));
        
    }
}
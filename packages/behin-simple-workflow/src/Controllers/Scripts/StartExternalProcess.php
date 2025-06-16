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
use Behin\SimpleWorkflow\Models\Entities;

class StartExternalProcess extends Controller
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
        $task = TaskController::getById("8bee90b3-6bc0-4537-86d0-715583566064");
        
        $parts = Entities\Parts::where('case_number', $case->number)->get();
        if(count($parts) == 0){
            $StoreAllPartInfo = new StoreAllPartInfo($case);
            $StoreAllPartInfo->execute();
        }
        
        //شروه فرایند جدید پذیرش در مدارپرداز
        $inbox = ProcessController::startFromScript(
            $task->id, 40, $case->number,
            $this->case->id
        );
        
        //ویرایش نام پرونده در ردیف کارتابل کارشناس
        $inbox->case_name = "ارجاع شده از فرایند داخلی | دستگاه: ";
        $inbox->case_name .= $case->getVariable('device_name');
        $inbox->save();
        
        $newCaseId = $inbox->case_id;
        $newCase = CaseController::getById($newCaseId);
        
        
        
        $newCase->saveVariable('customer_workshop_or_ceo_name', $case->getVariable('customer_workshop_or_ceo_name'));
        
        $newCase->saveVariable('customer_mobile', $case->getVariable('customer_mobile'));
        
        $newCase->saveVariable('device_name', $case->getVariable('device_name'));
        
        $newCase->saveVariable('device_model', $case->getVariable('device_model'));
        
        $newCase->saveVariable('device_control_system', $case->getVariable('device_control_system'));
        
        $newCase->saveVariable('device_control_model', $case->getVariable('device_control_model'));
        
        $newCase->saveVariable('mapa_serial', $case->getVariable('mapa_serial'));
        
        $newCase->saveVariable('has_electrical_map', $case->getVariable('has_electrical_map'));
        $newCase->saveVariable('receiver', $case->getVariable('receiver'));
        $newCase->saveVariable('packing', $case->getVariable('packing'));
        $newCase->saveVariable('admision_date', $case->getVariable('admision_date'));
        
    }
}
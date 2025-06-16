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

class StartInternalFixing extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        $mapaExpertHead = $case->getVariable('mapa_expert_head');
        
        //مرحله تعیین مسئول کار در فرایند تعمیرات داخلی
        $task = TaskController::getById("7b96d0c0-e2aa-43d2-bcda-67bcfb4b8c87");
        
        //شروه فرایند جدید پذیرش در مدارپرداز
        $inbox = ProcessController::startFromScript(
            $task->id, 
            1,//$mapaExpertHead,
            $case->number,
            $case->id
        );
        
        //ویرایش نام پرونده در ردیف کارتابل کارشناس
        InboxController::editCaseName(
            $inbox->id,
            $case->getVariable('customer_workshop_or_ceo_name') . ' | دستگاه: ' .
            $case->getVariable('device_name')
            );
        
        //ذخیره برخی از اطلاعات مشتری در پرونده داخلی
        $newCaseId = $inbox->case_id;
        $newCase = CaseController::getById($newCaseId);
        $newCase->saveVariable('customer_id', $case->getVariable('customer_id'));
        $newCase->saveVariable('device_id', $case->getVariable('device_id'));
        $newCase->saveVariable('part_id', $case->getVariable('part_id'));
        $newCase->saveVariable('mapa_expert_head', $case->getVariable('mapa_expert_head'));
 
    }
}
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
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StartInternalRepairForEachPart extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        $parts = Parts::where('case_number', $case->number)->whereNull('repair_is_approved')->get();
        if(count($parts) == 0){
            return "هیچ قطعه ای ثبت نشده است. دقت کنید که حتما پس از وارد کردن اطلاعات قطعه دکمه ثبت قطعه را بزنید";
        }
        
        foreach($parts as $part){
            
            //مرحله تعیین مسئول کار در فرایند تعمیرات داخلی
            $task = TaskController::getById("7b96d0c0-e2aa-43d2-bcda-67bcfb4b8c87");
            if($part->refer_to_unit == 'کنترل'){
                $mapaExpertHead = 12; // خانم طالبی
            }else{
                $mapaExpertHead = $part->mapa_expert_head;
            }
            
            //شروه فرایند جدید پذیرش در مدارپرداز
            $inbox = ProcessController::startFromScript(
                $task->id, 
                $mapaExpertHead,
                $case->number,
                $case->id
            );
            
            //ویرایش نام پرونده در ردیف کارتابل کارشناس
            $caseName = $case->getVariable('customer_workshop_or_ceo_name') . ' | دستگاه: ' .
                $case->getVariable('device_name');
            InboxController::editCaseName(
                $inbox->id,
                $caseName
                );
            
            $newCaseId = $inbox->case_id;
            $newCase = CaseController::getById($newCaseId);
            $newCase->name = $caseName;
            $newCase->copyVariableFrom($case->id);
            $newCase->saveVariable('mapa_expert_definer', $mapaExpertHead);
            $newCase->saveVariable('customer_workshop_or_ceo_name', $case->getVariable('customer_workshop_or_ceo_name'));
            $newCase->saveVariable('customer_id', $case->getVariable('customer_id'));
            $newCase->saveVariable('device_id', $case->getVariable('device_id'));
            $newCase->saveVariable('initial_device_piv', $case->getVariable('initial_device_piv'));
            $newCase->saveVariable('part_id', $part->id);
            
            //ذخیره اطلاعات دستگاه در فرایند جدید
            $newCase->saveVariable('part_name', $part->name);
            $newCase->saveVariable('mapa_serial', $part->mapa_serial);
            $newCase->saveVariable('initial_part_pic', $part->initial_part_pic);
            $newCase->saveVariable('refer_to_unit', $part->refer_to_unit);
            $newCase->saveVariable('has_attachment', $part->has_attachment);
            $newCase->saveVariable('attachment_image', $part->attachment_image);
            $newCase->saveVariable('mapa_expert_head', $part->mapa_expert_head);
            $newCase->saveVariable('device_name', $case->getVariable('device_name'));
            $newCase->saveVariable('device_model', $case->getVariable('device_model'));
            $newCase->saveVariable('device_control_system', $case->getVariable('device_control_system'));
            $newCase->saveVariable('device_control_model', $case->getVariable('device_control_model'));
            
            $newCase->saveVariable('has_electrical_map', $case->getVariable('has_electrical_map'));
            $newCase->saveVariable('initial_device_piv', $case->getVariable('initial_device_piv'));
            $newCase->saveVariable('mapa_expert_head_for_internal_process', $case->getVariable('mapa_expert_head_for_internal_process'));
            
        }
        
 
    }
}
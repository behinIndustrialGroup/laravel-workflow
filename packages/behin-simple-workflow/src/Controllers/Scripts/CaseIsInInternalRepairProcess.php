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
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Entities\Parts;
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
        $mainCase = Cases::where('process_id', '4bb6287b-9ddc-4737-9573-72071654b9de')->where('number', $requestedCaseNumber)->first();
        
        $cases = Cases::where('number', $requestedCaseNumber)->pluck('id');
        $currentTasks = Inbox::where('status', 'new')->whereIn('case_id', $cases)->pluck('task_id');
        
        //اگر تسک جاری ای وجود نداشت یعنی پرونده بسته شده است
        if( !count($currentTasks) ){
            return 'پرونده در تعمیرات داخل قرار ندارد';
        }
        
        //تمام تسک های جاری را بررسی کن که پرونده در کدام مرحله است
        foreach($currentTasks as $taskId){
            $processId = Task::find($taskId)->process_id;
            if(
                $processId != "ee209b0a-251c-438e-ab14-2018335eba6d" or 
                in_array($processId, [
                    "4bb6287b-9ddc-4737-9573-72071654b9de",
                    "35a5c023-5e85-409e-8ba4-a8c00291561c"
                    ])
                ){
                    return 'پرونده در تعمیرات داخل قرار ندارد';
                }
        }

        $case->number = $case->getVariable('case_number');
        $case->save();
        Parts::create([
            'case_id' => $case->id,
            'case_number' => $case->getVariable('case_number'),
            'name' => $case->getVariable('part_name'),
            'mapa_serial' => $case->getVariable('mapa_serial'),
            'mapa_expert_head' => $case->getVariable('mapa_expert_head_for_internal_process'),
            'refer_to_unit' => $case->getVariable('refer_to_unit'),
            'initial_part_pic' => $case->getVariable('initial_part_pic'),
            'has_attachment' => $case->getVariable('has_attachment'),
            'attachment_image' => $case->getVariable('attachment_image'),
            ]);
            
        $case->copyVariableFrom($mainCase->id, '', [
            'customer_id',
            'customer_workshop_or_ceo_name',
            'customer_city',
            'admision_date',
            'receiver',
            'packing',
            'initial_description',
            'device_id',
            'device_name',
            'device_model',
            'device_control_system',
            'device_control_model',
            'has_electrical_map',
            ]);
        $newCase = new StartInternalRepairForEachPart($case);
        $newCase->execute();
        
        // $case->parent_id = 
    }
}
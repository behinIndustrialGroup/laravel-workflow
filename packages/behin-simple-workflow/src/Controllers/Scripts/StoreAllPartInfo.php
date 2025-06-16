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

class StoreAllPartInfo extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        $part = Entities\Parts::updateOrCreate(
            [
                'id'=> $case->getVariable('part_id'),
                'case_number' => $case->number,
                'device_id' => $case->getVariable('device_id'),   
            ],
            [
                'name' => $case->getVariable('part_name'),
                'serial' => $case->getVariable('part_serial'),
                'mapa_expert_head' => $case->getVariable('mapa_expert_head_for_internal_process'),
                'mapa_expert' => $case->getVariable('mapa_expert'),
                'refer_to_unit' => $case->getVariable('refer_to_unit'),
                'repair_duration' => $case->getVariable('repair_duration'),
                'see_the_problem' => $case->getVariable('see_the_problem'),
                'fix_report' => $case->getVariable('fix_report'),
                'final_result_and_test' => $case->getVariable('final_result_and_test'),
                'test_possibility' => $case->getVariable('test_possibility'),
                'problem_seeing' => $case->getVariable('problem_seeing'),
                'final_result' => $case->getVariable('final_result'),
                'sending_for_test_and_troubleshoot' => $case->getVariable('sending_for_test_and_troubleshoot'),
                'test_in_another_place' => $case->getVariable('test_in_another_place'),
                'job_rank' => $case->getVariable('job_rank'),
                'repair_is_approved' => $case->getVariable('repair_is_approved'),
                'other_parts' => $case->getVariable('other_parts') ?? '', 
                'special_parts' => $case->getVariable('special_parts') ?? '', 
                'power' => $case->getVariable('power') ?? '', 
                'repair_duration' => $case->getVariable('repair_duration'), 
                'see_the_problem' => $case->getVariable('see_the_problem') ?? '', 
                'fix_report' => $case->getVariable('fix_report'), 
            ]
        );
        
    }
}
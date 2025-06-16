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
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities;



class StoreFixApproval extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        
        $partId = $case->getVariable('part_id');
        $part = Entities\Parts::find($partId);
        
        $part->final_result_and_test = $case->getVariable('final_result_and_test');
        $part->test_possibility = $case->getVariable('test_possibility');
        $part->problem_seeing = $case->getVariable('problem_seeing');
        $part->final_result = $case->getVariable('final_result');
        $part->sending_for_test_and_troubleshoot = $case->getVariable('sending_for_test_and_troubleshoot');
        $part->test_in_another_place = $case->getVariable('test_in_another_place');
        $part->job_rank = $case->getVariable('job_rank');
        $part->repair_is_approved = $case->getVariable('repair_is_approved');
        $part->dispatched_expert = $case->getVariable('dispatched_expert');
        $part->dispatched_expert_needed = $case->getVariable('dispatched_expert_needed');
        $part->mapa_expert_companions = $case->getVariable('mapa_expert_companions');
        $part->dispatched_expert_description = $case->getVariable('dispatched_expert_description');
        $part->save();
        
    }
}
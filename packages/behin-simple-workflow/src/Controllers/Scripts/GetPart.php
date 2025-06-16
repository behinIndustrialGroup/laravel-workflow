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



class GetPart extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        // $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        if($request->caseId and $request->caseId != 'undefined'){
            $caseId = $request->caseId;
            $case = CaseController::getById($caseId);
            $parentCase = CaseController::getById($case->parent_id);
            $partId = $case->getVariable('part_id');
            $part = Entities\Parts::find($partId);
            $case->saveVariable('part_name', $part->name);
            $case->saveVariable('part_serial', $part->serial);
            $case->saveVariable('mapa_serial', $part->mapa_serial);
            $case->saveVariable('refer_to_unit', $part->refer_to_unit);
            $case->saveVariable('has_attachment', $part->has_attachment);
            $case->saveVariable('mapa_expert_head', $part->mapa_expert_head);
            $case->saveVariable('initial_part_pic', $part->initial_part_pic);
            $case->saveVariable('device_name', $parentCase->getVariable('device_name'));
            $case->saveVariable('device_model', $parentCase->getVariable('device_model'));
            $case->saveVariable('device_control_system', $parentCase->getVariable('device_control_system'));
            $case->saveVariable('device_control_model', $parentCase->getVariable('device_control_model'));
            
            $case->saveVariable('has_electrical_map', $parentCase->getVariable('has_electrical_map'));
            return $part;
        }
        
        
    }
}
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



class GetCaseDevices extends Controller
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
            $devices = Entities\Devices::where('case_number', $case->number)->get();
            if(count($devices)){
                return $devices;
            }
            return (object)[
                [
                    'name' => $case->getVariable('device_name'),
                    'model' => $case->getVariable('device_model'),
                    'control_system' => $case->getVariable('control_system'),
                    'control_system_model' => $case->getVariable('control_system_model'),
                    'serial' => $case->getVariable('serial'),
                    'has_electrical_map' => $case->getVariable('has_electrical_map'),
                    'mapa_serial' => $case->getVariable('mapa_serial'),
                    'mapa_expert_head' => $case->getVariable('mapa_expert_head'),
                    'repair_is_approved' => $case->getVariable('repair_is_approved'),
                    'dispatched_expert' => $case->getVariable('dispatched_expert'),
                    'dispatched_expert_needed' => $case->getVariable('dispatched_expert_needed'),
                    ]    
            ];
        }
        
        
    }
}
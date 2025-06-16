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



class StorePart extends Controller
{
    private $case;
    public function __construct($case = null, Request $request= null)
    {
        if($case->id){
            $this->case = CaseController::getById($case->id);
        }else{
            $this->case = CaseController::getById($request->caseId);
        }
        
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $vars = [
            'case_id'=> $case->id,
            'case_number' => $case->number,
            'device_id' => $case->getVariable('device_id'),
            'name' => $case->getVariable('part_name'),
            'serial' => $case->getVariable('part_serial'),
            'mapa_expert_head' => $case->getVariable('mapa_expert_head'),
            'refer_to_unit' => $case->getVariable('refer_to_unit'),
        ];
        
        $part = Entities\Parts::updateOrCreate($vars);
        $case->saveVariable('part_id', $part->id);
        
        //خالی کردن مقادیر قطعه
        $case->saveVariable('part_name', '');
        $case->saveVariable('part_serial', '');
        $case->saveVariable('mapa_expert_head', '');
        $case->saveVariable('refer_to_unit', '');
        
    }
}
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
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities;



class GetDeviceParts extends Controller
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
            $caseNumber = $case->number;
            // $deviceId = $case->getVariable('device_id');
            $parts = Entities\Parts::where('case_number', $case->number)->get()
                ->each(function($row){
                    $row->mapa_expert_head = $row->mapaExpertHead();
                    $row->mapa_expert = $row->mapaExpert();
                    $row->dispatched_expert = getUserInfo($row->dispatched_expert);
                    $row->experts = $row->experts();
                });
            return $parts;
        }
        
        
    }
}
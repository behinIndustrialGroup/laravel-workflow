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
use Behin\SimpleWorkflow\Models\Entities\Mapa_center_fix_report;




class GetMapaCenterRepairReport extends Controller
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
            return $reports = Mapa_center_fix_report::where('case_number', $case->number)->get()->each(function($row){
                $row->duration = round(($row->end - $row->start) / 3600, 2) ;
                $row->start = toJalali((int) $row->start)->format('Y-m-d H:i');
                $row->end = toJalali((int) $row->end)->format('Y-m-d H:i');
                
                $row->expert = getUserInfo($row->expert)?->name;
            });
        }
        
        
    }
}
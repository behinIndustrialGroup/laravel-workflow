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
use Behin\SimpleWorkflow\Models\Core\Cases;



class RemoveCase extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        if($case){
            $this->case = CaseController::getById($case->id);
        }
        
    }

    public function execute(Request $request = null)
    {
        if($this->case){
            //این قسمت به صورت فرایند است و شماره پرونده ای که قرار است حذف شود وارد میشود
            $case = $this->case;
            $removeCaseNumber = $case->getVariable('case_number');
            $removeCase = Cases::where('number', $removeCaseNumber)->first();
            if(!$removeCase){
                return "پرونده پیدا نشد";
            }
            $inboxes = $removeCase->whereIs();
            foreach($inboxes as $inbox){
                if(isset($inbox->id)){
                    $inbox->delete();
                }
            }
            $case->saveVariable('remove_cause', "حذف شده به صورت دستی توسط: ". Auth::user()->name);
            $removeCase->delete();
        }
        if(isset($request->caseId)){
            //این قسمت برای دکمه داخل پرونده هست و همان پرونده حذف میشود
            $caseId = $request->caseId;
            $case = CaseController::getById($caseId);
            $removeCaseNumber = $case->number;
            $removeCase = Cases::where('number', $removeCaseNumber)->first();
            if(!$removeCase){
                return "پرونده پیدا نشد";
            }
            $inboxes = $removeCase->whereIs();
            foreach($inboxes as $inbox){
                if(isset($inbox->id)){
                    $inbox->status= "done";
                    $inbox->save();
                }
            }
            $case->saveVariable('remove_cause', "حذف شده به صورت دستی توسط: ". Auth::user()->name);
            // $removeCase->delete();
        }
        
        
        
    }
}
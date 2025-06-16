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
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities;



class StoreFixReport extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        
        $part = Entities\Parts::find($case->getVariable('part_id'));
        $partReport = Part_reports::where('case_number', $case->number)->count();
        if(!$partReport){
            return "هیج گزارش تعمیری برای این پرونده ثبت نشده است. لطفا از دکمه ذخیره و افزودن گزارش تعمیر روزانه استفاده کنید";
        }
        // $doneDate = convertPersianToEnglish($case->getVariable('done_date'));
        // if(strlen($doneDate) != 10){
        //     return "تاریخ را درست انتخاب کنید";
        // }
        
        // $doneTime = convertPersianToEnglish($case->getVariable('done_time'));
        // if(strlen($doneTime) != 5 or !str_contains($doneTime, ':')){
        //     return "ساعت را درست انتخاب کنید";
        // }
        
        // $doneAt = Jalalian::fromFormat('Y-m-d H:i', "$doneDate $doneTime")->toCarbon()->timestamp;

        
        // $part->other_parts = $case->getVariable('other_parts');
        // $part->special_parts = $case->getVariable('special_parts') ;
        // $part->power = $case->getVariable('power');
        // $part->repair_duration = $case->getVariable('repair_duration');
        // $part->see_the_problem = $case->getVariable('see_the_problem');
        // $part->fix_report = $case->getVariable('fix_report');
        // $part->done_at = $doneAt;
        // $part->save();
    }
}
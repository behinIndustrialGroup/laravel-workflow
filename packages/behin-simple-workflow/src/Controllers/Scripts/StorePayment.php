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
use Behin\SimpleWorkflow\Models\Entities\Financials;



class StorePayment extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $fins = Financials::where('case_number', $case->number)->get();
        
        foreach($fins as $fin){
            if (str_contains($fin->fix_cost_type, 'نقدی')){
                if($fin->payment == null){
                    return "برای تمام ردیف های مالی که به صورت نقدی دریافت شده اند باید هزینه دریافت شده را وارد کنید";
                }
                if(!$fin->payment_date){
                    return "برای تمام ردیف های مالی که به صورت نقدی دریافت شده اند باید تاریخ دریافت هزینه را وارد کنید";
                }
            }
        }
        // $deviceId = $case->getVariable('device_id');
        // $finId = $case->getVariable('financial_id');
        // $fin = Financials::where('case_id', $case->id)->first();
        // if(!$fin){
        //     $fin = new Financials();
        // }
        
        // if($fin->cost){
        //     $storeFixCost = new StoreFixCost($case);
        //     $storeFixCost->execute();
        // }
        // $payment_date = convertPersianToEnglish($case->getVariable('payment_date'));
        // $payment_date = Jalalian::fromFormat('Y-m-d', $payment_date)->toCarbon()->timestamp;
        
        // $fin->case_number = $case->number;
        // $fin->case_id= $case->id;
        // $fin->process_name = $case->process->name;
        // $fin->payment = $case->getVariable('payment_amount');
        // $fin->payment_date = $payment_date;
        // $fin->description .= $case->getVariable('payment_amount_description');
        // $fin->payment_after_completion = $case->getVariable('payment_after_completion');
        // $fin->save();
        
        // $case->saveVariable('financial_id', $fin->id);
    }
}
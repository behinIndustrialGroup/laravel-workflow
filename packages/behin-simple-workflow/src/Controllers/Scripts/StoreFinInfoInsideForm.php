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
use BehinFileControl\Controllers\FileController;



class StoreFinInfoInsideForm extends Controller
{
    private $case;
    public function __construct()
    {
        
    }

    public function execute(Request $request = null)
    {
        $caseId = $request->caseId;
        $case = $this->case = CaseController::getById($caseId);
        $type = $request->fix_cost_type;
        $amount_announcement_date = $request->amount_announcement_date;
        $amount_announcement_date = convertPersianToEnglish($amount_announcement_date);
        $amount_announcement_date = Jalalian::fromFormat('Y-m-d', "$amount_announcement_date")->toCarbon()->timestamp;
        $amount = $request->amount;
        $bank_account_destinatio_name = $request->bank_account_destinatio_name;
        $account_number = $request->account_number;
        $repair_cost_description = $request->repair_cost_description;
        
        if($type == "علی الحساب - نقدی" or $type == "تسویه کامل - نقدی"){
            Financials::create([
                'case_number' => $case->number,
                'case_id' => $case->id,
                'process_name' => $case->process->name,
                'process_id' => $case->process->id,
                'fix_cost_type' => $type,
                'cost' => $amount,
                'fix_cost_date' => $amount_announcement_date,
                'destination_account' => $account_number,
                'destination_account_name' => $bank_account_destinatio_name,
                'description' => $repair_cost_description
            ]);
        }
        
        if($type == "علی الحساب - چک" or $type == "تسویه کامل - چک"){
            $cheque_number = $request->cheque_number;
            $cheque_number = convertPersianToEnglish($cheque_number);
            $cheque_due_date = $request->cheque_due_date;
            $cheque_due_date = convertPersianToEnglish($cheque_due_date);
            $cheque_due_date = Jalalian::fromFormat('Y-m-d', "$cheque_due_date")->toCarbon()->timestamp;
            Financials::create([
                'case_number' => $case->number,
                'case_id' => $case->id,
                'process_name' => $case->process->name,
                'process_id' => $case->process->id,
                'fix_cost_type' => $type,
                'cost' => $amount,
                'fix_cost_date' => $amount_announcement_date,
                'destination_account' => $account_number,
                'destination_account_name' => $bank_account_destinatio_name,
                'description' => $repair_cost_description,
                'cheque_due_date' => $cheque_due_date,
                'cheque_number' => $cheque_number
            ]);
        }
        
        if($type == "حساب دفتری"){
            Financials::create([
                'case_number' => $case->number,
                'case_id' => $case->id,
                'process_name' => $case->process->name,
                'process_id' => $case->process->id,
                'fix_cost_type' => $type,
                'cost' => $amount,
                'fix_cost_date' => $amount_announcement_date,
                'destination_account' => $account_number,
                'destination_account_name' => $bank_account_destinatio_name,
                'description' => $repair_cost_description
            ]);
        }
    }
}
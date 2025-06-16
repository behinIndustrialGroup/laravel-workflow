<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class StoreFixCost extends Controller
{
    private $case;

    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        // $case = $this->case;

        // // دریافت اطلاعات مربوط به هزینه ثابت
        // $fixCostDate = convertPersianToEnglish($case->getVariable('fix_cost_date'));
        // $fixCostDate = Jalalian::fromFormat('Y-m-d', $fixCostDate)->toCarbon()->timestamp;

        // $fin = Financials::where('case_id', $case->id)->first() ?? new Financials();

        // // هزینه 1
        // if ($case->getVariable('fix_cost')) {
        //     $fin->cost = $case->getVariable('fix_cost');
        //     $fin->destination_account = $case->getVariable('account_number');
        //     $fin->destination_account_name = $case->getVariable('bank_account_destinatio_name');
        // }

        // // هزینه 2
        // if ($case->getVariable('fix_cost_2')) {
        //     $fin->cost2 = $case->getVariable('fix_cost_2');
        //     $fin->destination_account_2 = $case->getVariable('account_number_2');
        //     $fin->destination_account_name_2 = $case->getVariable('bank_account_destinatio_name_2');
        // }

        // // هزینه 3
        // if ($case->getVariable('fix_cost_3')) {
        //     $fin->cost3 = $case->getVariable('fix_cost_3');
        //     $fin->destination_account_3 = $case->getVariable('account_number_3');
        //     $fin->destination_account_name_3 = $case->getVariable('bank_account_destinatio_name_3');
        // }

        // // سایر اطلاعات مالی
        // $fin->fix_cost_date = $fixCostDate;
        // $fin->fix_cost_type = $case->getVariable('fix_cost_type');
        // $fin->description = $case->getVariable('repair_cost_description');
        // $fin->case_id = $case->id;
        // $fin->case_number = $case->number;
        // $fin->process_id = $case->process_id;
        // $fin->process_name = $case->process->name;

        // // ذخیره در دیتابیس
        // $fin->save();

        // // ذخیره متغیر شناسه مالی
        // $case->saveVariable('financial_id', $fin->id);
    }
}
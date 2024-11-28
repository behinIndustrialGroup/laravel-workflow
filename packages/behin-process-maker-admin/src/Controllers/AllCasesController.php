<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\PMUserController;
use BehinProcessMaker\Models\PmVars;

class AllCasesController extends Controller
{
    public function allCasesForm(){
        return view('PMAdminViews::list');
    }

    public static function all(){
        $cases = PmVars::groupBy('process_id', 'case_id')->get();
        $data = [];
        foreach($cases as $case){
            $search = PmVars::where('case_id', $case->case_id);

            $customer_name = PmVars::where('case_id', $case->case_id)->where('key', 'customer_fullname')->first()?->value;
            $app_number = PmVars::where('case_id', $case->case_id)->where('key', 'app_number')->first()?->value;
            $case_number = PmVars::where('case_id', $case->case_id)->where('key', 'case_number')->first()?->value;
            $device_name = PmVars::where('case_id', $case->case_id)->where('key', 'device_name')->first()?->value;
            $receive_date = PmVars::where('case_id', $case->case_id)->where('key', 'receive_date')->first()?->value;
            $customer_mobile = PmVars::where('case_id', $case->case_id)->where('key', 'customer_mobile')->first()?->value;
            $repair_report = PmVars::where('case_id', $case->case_id)->where('key', 'repair_report')->first()?->value;
            $repair_is_approved = PmVars::where('case_id', $case->case_id)->where('key', 'repair_is_approved')->first()?->value;
            $repair_declined_description = PmVars::where('case_id', $case->case_id)->where('key', 'repair_declined_description')->first()?->value;
            $repair_is_approved_2 = PmVars::where('case_id', $case->case_id)->where('key', 'repair_is_approved_2')->first()?->value;
            $repair_declined_description_2 = PmVars::where('case_id', $case->case_id)->where('key', 'repair_declined_description_2')->first()?->value;
            $repair_is_approved_3 = PmVars::where('case_id', $case->case_id)->where('key', 'repair_is_approved_3')->first()?->value;
            $repair_declined_description_3 = PmVars::where('case_id', $case->case_id)->where('key', 'repair_declined_description_3')->first()?->value;
            $repair_cost = PmVars::where('case_id', $case->case_id)->where('key', 'repair_cost')->first()?->value;
            $payment_amount = PmVars::where('case_id', $case->case_id)->where('key', 'payment_amount')->first()?->value;
            $repairman_id = PmVars::where('case_id', $case->case_id)->where('key', 'repairman')->first()?->value;
            $repairman = PMUserController::getUserByPmUserId($repairman_id);
            $status = PmVars::where('case_id', $case->case_id)->where('key', 'last_status')->first()?->value;
            // $caseinfo = CaseInfoController::get($case->case_id);
            $data[]= [
                'process_id' => $case->process_id,
                'case_id' => $case->case_id,
                'customer_fullname' => $customer_name,
                'app_number' => $app_number,
                'case_number' => $case_number,
                'receive_date' => $receive_date,
                'repair_report' => $repair_report,
                'device_name' => $device_name,
                'repairman' => $repairman?->name,
                'repair_is_approved' => $repair_is_approved,
                'repair_declined_description' => $repair_declined_description,
                'repair_is_approved_2' => $repair_is_approved_2,
                'repair_declined_description_2' => $repair_declined_description_2,
                'repair_is_approved_3' => $repair_is_approved_3,
                'repair_declined_description_3' => $repair_declined_description_3,
                'repair_cost' => $repair_cost,
                'payment_amount' => $payment_amount,
                // 'caseInfo' => $caseinfo,
                'status' => $status,
            ];
        }
        return [
            'data' => $data
        ];
    }
}

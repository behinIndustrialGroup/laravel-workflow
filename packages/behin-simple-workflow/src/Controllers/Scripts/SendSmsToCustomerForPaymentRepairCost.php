<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Http\Request;

class SendSmsToCustomerForPaymentRepairCost extends Controller
{
    protected $case;
    public function __construct($case = null)
    {
        $this->case = $case;
        // return VariableController::save(
        //     $this->case->process_id, $this->case->id, 'manager', 2
        // );
    }

    public function execute(Request $request = null)
    {
        if($request->caseId && $request->caseId != 'undefined'){
            $caseId = $request->caseId;
            $case = CaseController::getById($caseId);
        }else{
            $case = $this->case;
        }
        $customer_mobile = $case->getVariable('customer_mobile');
        $fix_cost = $case->getVariable('fix_cost');
        $account_number = $case->getVariable('account_number');
        if($customer_mobile && $fix_cost && $account_number){
            
                $params = array([
                    "name" => "FIX_COST",
                    "value" => $fix_cost
                ],
                [
                    "name" => "ACCOUNT_NUMBER",
                    "value" => $account_number
                ]);
                $result = SmsController::sendByTemp($customer_mobile, 285563, $params);
                $number_of_fin_sms_sended = $case->getVariable('number_of_fin_sms_sended');
                $case->saveVariable('number_of_fin_sms_sended', (int) $number_of_fin_sms_sended +1 );
            
        }else{
            
            $msg = "یکی از فیلدهای موبایل مشتری یا هزینه تعیین شده یا شماره حساب خالی است";
            
            return response($msg, 402);
        }
    }
}
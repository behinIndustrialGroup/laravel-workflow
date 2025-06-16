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
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Carbon;

class SendValidationSmsToCustomer extends Controller
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
        if(isset($request->caseId) && $request->caseId != 'undefined'){
            $caseId = $request->caseId;
            $case = CaseController::getById($caseId);
        }else{
            $case = $this->case;
        }
        $customer_mobile = $case->getVariable('customer_mobile');
        $partLeft = $case->getVariable('part_left_from_customer_location');
        $needNextVisit = $case->getVariable('need_next_visit');
        
        if($customer_mobile){
            $validationCode = rand(10000,99999);
            $sendDate = Carbon::now()->timestamp;
            $case->saveVariable('validation_code', $validationCode);
            $case->saveVariable('validation_code_send_date', $sendDate);
                $params = array([
                    "name" => "CODE",
                    "value" => $validationCode
                ]);
                $result = SmsController::sendByTemp($customer_mobile, 134614, $params);
            
        }else{
            
            $msg = "یکی از فیلدهای موبایل مشتری یا هزینه تعیین شده یا شماره حساب خالی است";
            
            return response($msg, 402);
        }
    }
}
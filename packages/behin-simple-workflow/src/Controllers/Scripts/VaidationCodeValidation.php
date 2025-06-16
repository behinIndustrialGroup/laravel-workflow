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

class VaidationCodeValidation extends Controller
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
        $case = $this->case;
        $validationCode = $case->getVariable('validation_code');
        $sendDate = (int)$case->getVariable('validation_code_send_date');
        $customerValidationCode = $case->getVariable('customer_validation_code');
        $customerSignature = $case->getVariable('customer_signature');
        $now = Carbon::now()->timestamp;
        // فعلا برای اینکه در پرونده های قدیمی برای مشتری کد تایید نرود این بخش را غیرفعال است
        // if(!$validationCode){
        //     return response("حداقل یکبار باید کد را برای مشتری ارسال کنید", 402);
        // }
        if($customerValidationCode){
            if($customerValidationCode != $validationCode){
                return "کدتایید وارد شده معتبر نیست";
            }
        }
        if( ($now - $sendDate) < 120 ){
            return "لطفا تا دو دقیقه منتظر بمانید. اگر پیامک ارسال نشده بود با دریافت امضا و زدن دکمه ذخیره و مرحله بعد کار را به پایان برسانید";
        }
        
        
        if(!$customerSignature){
            return "امضای مشتری اجباریست";
        }
        $case->saveVariable('validation_code', '');
    }
}
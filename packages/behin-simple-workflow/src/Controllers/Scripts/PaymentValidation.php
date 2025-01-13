<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;

class PaymentValidation extends Controller
{
    protected $case;
    public function __construct($case) {
        $this->case = $case;
        
    }

    public function execute()
    {
        $variables = VariableController::getVariablesByCaseId($this->case->id);

        $payment_amount = $variables->where('key', 'payment_amount')->first()?->value;
        $payment_amount = str_replace(',', '', $payment_amount);
        $payment_amount = str_replace('/', '', $payment_amount);
        $payment_amount = str_replace('.', '', $payment_amount);
        $payment_amount = str_replace(' ', '', $payment_amount);
        $payment_amount = str_replace('ریال', '', $payment_amount);
        if(str_contains($payment_amount, 'تومان')){
            return trans('fields.مبلغ را به ریال وارد کنید');
        }
        if(!is_numeric($payment_amount)){
            return trans('fields.payment_amount_is_not_numeric');
        }
        
    }

}

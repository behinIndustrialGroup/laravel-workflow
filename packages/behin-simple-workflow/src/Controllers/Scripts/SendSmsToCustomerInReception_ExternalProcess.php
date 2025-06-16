<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Http\Request;

class SendSmsToCustomerInReception_ExternalProcess extends Controller
{
    protected $case;
    public function __construct($case)
    {
        $this->case = $case;
        // return VariableController::save(
        //     $this->case->process_id, $this->case->id, 'manager', 2
        // );
    }

    public function execute()
    {
        $caseNumber = $this->case->number;
        $variables = $this->case->variables();
        $customerMobile = $variables->where('key', 'customer_mobile')->first()?->value;
        if ($customerMobile) {
            $params = array([
                "name" => "CASENUMBER",
                "value" => $caseNumber
            ]);
            $result = SmsController::sendByTemp($customerMobile, 653619, $params);
        }
    }
}
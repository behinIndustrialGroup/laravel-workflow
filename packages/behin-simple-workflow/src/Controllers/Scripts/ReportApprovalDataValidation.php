<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Http\Request;

class ReportApprovalDataValidation extends Controller
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
        $variables = $this->case->variables();
        $dispatched_expert_needed = $variables->where('key', 'dispatched_expert_needed')->first()?->value;
        $dispatched_expert = $variables->where('key', 'dispatched_expert')->first()?->value;
        if($dispatched_expert_needed == 'بله'){
            if(!$dispatched_expert){
                return "کارشناس جهت اعزام تعیین نشده است";
            }
        }
        
    }
}
<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Http\Request;

class SendSmsToExpertHead extends Controller
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
        $expert_head = $variables->where('key', 'mapa_expert_head')->first()?->value;
        if($expert_head){
            $user = getUserInfo($expert_head);
            if($user){
                $params = array([
                    "name" => "NAME",
                    "value" => $user?->name
                ]);
                $result = SmsController::sendByTemp($user->email, 140535, $params);
            }
        }
        
    }
}
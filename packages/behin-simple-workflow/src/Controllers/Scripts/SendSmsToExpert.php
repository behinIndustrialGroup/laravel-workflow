<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Http\Request;

class SendSmsToExpert extends Controller
{
    protected $case;
    public function __construct($case)
    {
        $this->case = $case;
    }

    public function execute()
    {
        $variables = $this->case->variables();
        $expert = $variables->where('key', 'mapa_expert')->first()?->value;
        if($expert){
            $user = getUserInfo($expert);
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
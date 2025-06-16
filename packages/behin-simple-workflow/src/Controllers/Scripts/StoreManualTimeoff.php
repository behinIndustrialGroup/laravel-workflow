<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Morilog\Jalali\Jalalian;



class StoreManualTimeoff extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $variables = $this->case->variables();
        $type = $variables->where('key', 'timeoff_request_type')->first()?->value;
        $userId = $case->getVariable('timeoff_user_number');
        $duration = $case->getVariable('timeoff_daily_request_duration');
        $uniqueId = rand(100000,1000000 );
        $now = Carbon::now();
        $now = toJalali($now);
        $requestDate = explode('-', $now);
        
        Timeoffs::updateOrCreate([
            'uniqueId' => $uniqueId
            ],
            [
            'user' => $userId,
            'type' => $type,
            'duration' => $duration,
            'request_day' => $requestDate[2],
            'request_month' => $requestDate[1],
            'request_year' => $requestDate[0],
            'approved' => 1
        ]);
        return "ثبت شد";
        
    }
}
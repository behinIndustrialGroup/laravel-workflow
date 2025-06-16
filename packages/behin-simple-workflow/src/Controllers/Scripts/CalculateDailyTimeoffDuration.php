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



class CalculateDailyTimeoffDuration extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        // $this->case = CaseController::getById($case->id);
    }

    public static function execute(Request $request = null)
    {
        // $variables = $this->case->variables();
        // $type = $variables->where('key', 'timeoff_request_type')->first()?->value;
        $date = $request->timeoff_start_date;
        $date= convertPersianToEnglish($date);
        $startDate = Jalalian::fromFormat('Y-m-d', $date)->toCarbon();
        $endDate = $request->timeoff_end_date;
        $endDate= convertPersianToEnglish($endDate);
        $endDate = Jalalian::fromFormat('Y-m-d', $endDate)->toCarbon();
        
        return $startDate->diffInDays($endDate) +1;
            
        
    }
}
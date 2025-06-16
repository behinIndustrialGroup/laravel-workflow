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
use Behin\SimpleWorkflowReport\Controllers\Core\TimeoffController;



class CalculateRestOfTimeoff extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        // $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $this->case = CaseController::getById($request->caseId);
        $userId = $this->case->creator;
        $restLeaves = TimeoffController::totalLeaves($userId)->first();
        if($restLeaves){
            return $restLeaves->restLeaves;
        }else{
            return "خطا";
        }
        // $user = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();
        $todayShamsi = Jalalian::fromCarbon($today);
        $thisYear = $todayShamsi->getYear();
        $thisMonth = $todayShamsi->getMonth();
        $nowMonth = toJalali($now)->getMonth();
        $total = 20 * $nowMonth; // هر شخص به ازای هر ماه 20 ساعت مرخصی دارد
        $dailyUsed = Timeoffs::where('user', $userId)->where('type', 'روزانه')->where('approved', 1)->where(function ($query) use ($thisYear) {
                $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
            })->sum('duration');
        $hourlyUsed = Timeoffs::where('user', $userId)->where('type', 'ساعتی')->where('approved', 1)->where(function ($query) use ($thisYear) {
                $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
            })->sum('duration');
        $used = ($dailyUsed * 8) + ($hourlyUsed);
        return round($total-$used, 2);
    }
}
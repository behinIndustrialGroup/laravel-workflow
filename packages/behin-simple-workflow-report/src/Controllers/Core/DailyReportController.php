<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\Mapa_center_fix_report;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use BehinUserRoles\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class DailyReportController extends Controller
{
    private $allowedProcessIds;
    public function __construct()
    {
        $this->allowedProcessIds = [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de',
            'ee209b0a-251c-438e-ab14-2018335eba6d'
        ];
    }
    public function index(Request $request)
    {
        $allowedProcessIds = $this->allowedProcessIds;

        // تاریخ امروز شمسی به فرمت Y-m-d
        $defaultFrom = Jalalian::now()->format('Y-m-d');
        $defaultTo = Jalalian::now()->format('Y-m-d');

        // اگر کاربر مقدار وارد نکرده باشه، تاریخ امروز در نظر گرفته میشه
        $from_input = convertPersianToEnglish($request->from_date ?? $defaultFrom);
        $to_input = convertPersianToEnglish($request->from_date ?? $defaultFrom);

        // تبدیل تاریخ شمسی به میلادی
        $from = Jalalian::fromFormat('Y-m-d', $from_input)->toCarbon();
        $to = Jalalian::fromFormat('Y-m-d', $to_input)->toCarbon()->endOfDay();

        $query = User::query();
        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get()->each(function ($row) use ($allowedProcessIds, $from, $to) {
            $internal = Part_reports::query();

            $external = Repair_reports::query();

            $mapa_center = Mapa_center_fix_report::query();

            if ($from) {
                $internal = $internal->whereDate('updated_at', '>=', $from);
                $external = $external->whereDate('updated_at', '>=', $from);
                $mapa_center = $mapa_center->whereDate('updated_at', '>=', $from);
            }

            if ($to) {
                $internal = $internal->whereDate('updated_at', '<=', $to);
                $external = $external->whereDate('updated_at', '<=', $to);
                $mapa_center = $mapa_center->whereDate('updated_at', '<=', $to);
            }
            $row->internal = $internal->where('registered_by', $row->id)->distinct('case_number')->count('case_number');
            $row->external = $external->where('mapa_expert', $row->id)->distinct('case_number')->count('case_number');
            $row->mapa_center = $mapa_center->where('expert', $row->id)->distinct('case_number')->count('case_number');
        });

        return view('SimpleWorkflowReportView::Core.DailyReport.index', compact('users'));
    }

    public function showInternal($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $from ? convertPersianToEnglish($from) : null;

        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = Part_reports::query();
        if ($from) {
            $query->whereDate('updated_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('updated_at', '<=', $to);
        }
        $items = $query->where('registered_by', $user_id)->groupBy('case_number')
            ->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show', compact('items'));
    }

    public function showExternal($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $from ? convertPersianToEnglish($from) : null;
        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = Repair_reports::query();
        if ($from) {
            $query->whereDate('updated_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('updated_at', '<=', $to);
        }
        $items = $query->where('mapa_expert', $user_id)->groupBy('case_number')
            ->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show-external', compact('items'));
    }

    public function showMapaCenter($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $from ? convertPersianToEnglish($from) : null;
        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = Mapa_center_fix_report::query();
        if ($from) {
            $query->whereDate('updated_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('updated_at', '<=', $to);
        }
        $items = $query->where('expert', $user_id)->groupBy('case_number')
            ->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show-mapa-center', compact('items'));
    }
}

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
use Behin\SimpleWorkflow\Models\Entities\Other_daily_reports;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Behin\SimpleWorkflowReport\Models\DailyReportReminderLog;
use Behin\SimpleWorkflowReport\Services\DailyReportReminderService;
use Behin\Sms\Controllers\SmsController;
use BehinUserRoles\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
use Throwable;

class DailyReportController extends Controller
{
    private $allowedProcessIds;
    private DailyReportReminderService $reminderService;

    public function __construct(DailyReportReminderService $reminderService)
    {
        $this->allowedProcessIds = [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de',
            'ee209b0a-251c-438e-ab14-2018335eba6d'
        ];

        $this->reminderService = $reminderService;
    }
    public function index(Request $request)
    {
        $allowedProcessIds = $this->allowedProcessIds;

        // تاریخ امروز شمسی به فرمت Y-m-d
        $defaultFrom = Jalalian::now()->format('Y-m-d');
        $defaultTo = Jalalian::now()->format('Y-m-d');

        // اگر کاربر مقدار وارد نکرده باشه، تاریخ امروز در نظر گرفته میشه
        $from_input = convertPersianToEnglish($request->from_date ?? $defaultFrom);
        $toSource = $request->to_date ?: $request->from_date ?: $defaultTo;
        $to_input = convertPersianToEnglish($toSource);

        // تبدیل تاریخ شمسی به میلادی
        $from = Jalalian::fromFormat('Y-m-d', $from_input)->toCarbon()->startOfDay();
        $to = Jalalian::fromFormat('Y-m-d', $to_input)->toCarbon()->endOfDay();

        $query = User::query()->orderBy('number', 'asc');
        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get()->each(function ($row) use ($allowedProcessIds, $from, $to) {
            $internal = Part_reports::query();

            $external = Repair_reports::query();
            $externalAsAssistant = Repair_reports::query();

            $mapa_center = Mapa_center_fix_report::query();
            $otherDailyReport = Other_daily_reports::query();

            if ($from) {
                $internal = $internal->whereDate('updated_at', '>=', $from);
                $external = $external->whereDate('created_at', '>=', $from);
                $externalAsAssistant = $externalAsAssistant->whereDate('created_at', '>=', $from);
                $mapa_center = $mapa_center->whereDate('updated_at', '>=', $from);
                $otherDailyReport = $otherDailyReport->whereDate('created_at', '>=', $from);
            }

            if ($to) {
                $internal = $internal->whereDate('updated_at', '<=', $to);
                $external = $external->whereDate('created_at', '<=', $to);
                $externalAsAssistant = $externalAsAssistant->whereDate('created_at', '<=', $to);
                $mapa_center = $mapa_center->whereDate('updated_at', '<=', $to);
                $otherDailyReport = $otherDailyReport->whereDate('created_at', '<=', $to);
            }
            $row->internal = $internal->where('registered_by', $row->id)->distinct('case_number')->count('case_number');
            $row->external = $external->where('mapa_expert', $row->id)->distinct('case_number')->count('case_number');
            $row->externalAsAssistant = $externalAsAssistant->where('mapa_expert_companions', 'LIKE', '%"' . $row->id . '"%')->distinct('case_number')->count('case_number');
            $row->mapa_center = $mapa_center->where('expert', $row->id)->distinct('case_number')->count('case_number');
            $row->other_daily_report = $otherDailyReport->where('created_by', $row->id)->distinct('case_number')->count('case_number');
        });

        $reportDate = $from->copy();

        // return (string)$from;
        $timeoffItems = TimeoffController::todayItems($reportDate->copy());
        $hourlyTimeoffItems = TimeoffController::todayHourlyItems($reportDate->copy());

        $reminderLogs = DailyReportReminderLog::query()
            ->where('report_date', $reportDate->toDateString())
            ->get()
            ->keyBy('user_id');

        return view('SimpleWorkflowReportView::Core.DailyReport.index', compact('users', 'timeoffItems', 'hourlyTimeoffItems', 'reminderLogs'));
    }

    public function showInternal($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $to ? convertPersianToEnglish($to) : null;

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
            ->orderBy('case_number', 'desc')->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show', compact('items'));
    }

    public function showExternal($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $to ? convertPersianToEnglish($to) : null;
        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = Repair_reports::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $items = $query->where('mapa_expert', $user_id)->groupBy('case_number')
            ->orderBy('case_number', 'desc')->get();

        $items->each(function($row){
            try{
            $startDate = convertPersianToEnglish($row->start_date);
            $startTime = convertPersianToEnglish($row->start_time);
            $startTime = str_replace('/', ':', $startTime);
            $row->start = Jalalian::fromFormat('Y-m-d H:i', "$startDate $startTime")->toCarbon()->timestamp;

            $endDate = convertPersianToEnglish($row->end_date);
            $endTime = convertPersianToEnglish($row->end_time);
            $endTime = str_replace('/', ':', $endTime);
            $row->end = Jalalian::fromFormat('Y-m-d H:i', "$endDate $endTime")->toCarbon()->timestamp;

            $row->duration = ((int)$row->end - (int)$row->start) / 3600; //به ساعت
            }catch(Exception $e){
                $row->start = null;
                $row->end = null;
                $row->duration = null;
            }
        });

        return view('SimpleWorkflowReportView::Core.DailyReport.show-external', compact('items'));
    }

    public function showMapaCenter($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $to ? convertPersianToEnglish($to) : null;
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
            ->orderBy('case_number', 'desc')->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show-mapa-center', compact('items'));
    }

    public function showExternalAsAssistant($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $to ? convertPersianToEnglish($to) : null;
        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = Repair_reports::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $items = $query->where('mapa_expert_companions', 'LIKE', '%"' . $user_id . '"%')->groupBy('case_number')
            ->orderBy('case_number', 'desc')->get();

        $items->each(function($row){
            try{
            $startDate = convertPersianToEnglish($row->start_date);
            $startTime = convertPersianToEnglish($row->start_time);
            $startTime = str_replace('/', ':', $startTime);
            $row->start = Jalalian::fromFormat('Y-m-d H:i', "$startDate $startTime")->toCarbon()->timestamp;

            $endDate = convertPersianToEnglish($row->end_date);
            $endTime = convertPersianToEnglish($row->end_time);
            $endTime = str_replace('/', ':', $endTime);
            $row->end = Jalalian::fromFormat('Y-m-d H:i', "$endDate $endTime")->toCarbon()->timestamp;

            $row->duration = ((int)$row->end - (int)$row->start) / 3600; //به ساعت
            }catch(Exception $e){
                $row->start = null;
                $row->end = null;
                $row->duration = null;
                Log::error($e);
            }
        });

        return view('SimpleWorkflowReportView::Core.DailyReport.show-external-as-assistant', compact('items'));
    }

    public function showOtherDailyReport($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $to ? convertPersianToEnglish($to) : null;
        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = Other_daily_reports::query();
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $items = $query->where('created_by', $user_id)->groupBy('case_number')
            ->orderBy('case_number', 'desc')->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show-other-daily-report', compact('items'));
    }

    public function sendReminder(Request $request)
    {
        $dateInput = $request->input('date');
        if ($dateInput) {
            try {
                $dateInput = convertPersianToEnglish($dateInput);
                $targetDate = Carbon::createFromFormat('Y-m-d', $dateInput)->startOfDay();
            } catch (Exception $exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'فرمت تاریخ وارد شده نامعتبر است.',
                ], 422);
            }
        } else {
            $targetDate = Carbon::today();
        }

        if ($targetDate->isFriday()) {
            return response()->json([
                'status' => 'skipped',
                'message' => 'ارسال پیامک یادآوری در روز جمعه انجام نمی‌شود.',
            ]);
        }

        $templateId = config('services.sms.daily_report_reminder_template_id');
        if (! $templateId) {
            return response()->json([
                'status' => 'error',
                'message' => 'قالب پیامک یادآوری گزارش روزانه تعریف نشده است.',
            ], 422);
        }

        $parameterKey = config('services.sms.daily_report_reminder_parameter_key', 'NAME');

        $reportingUsers = $this->reminderService->getUsersWithReports($targetDate);
        $dailyLeaveUsers = TimeoffController::todayItems($targetDate->copy())
            ->filter(function ($timeoff) {
                return $timeoff->type === 'روزانه';
            })
            ->pluck('user')
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values();

        $users = User::query()
            ->where('sms_reminder_enabled', true)
            ->orderBy('number', 'asc')
            ->get();

        $usersToNotify = $users->filter(function ($user) use ($reportingUsers, $dailyLeaveUsers) {
            if (! $user->email) {
                return false;
            }

            $userId = (int) $user->id;

            return ! $reportingUsers->contains($userId)
                && ! $dailyLeaveUsers->contains($userId);
        })->values();

        $sent = [];
        $failed = [];
        $notifiedUsers = [];

        foreach ($usersToNotify as $user) {
            try {
                $parameters = [[
                    'name' => $parameterKey,
                    'value' => $user->name,
                ]];
                $response = SmsController::sendByTemp($user->email, $templateId, $parameters);

                $log = DailyReportReminderLog::updateOrCreate(
                    [
                        'report_date' => $targetDate->toDateString(),
                        'user_id' => $user->id,
                    ],
                    [
                        'mobile' => $user->email,
                        'status' => DailyReportReminderLog::STATUS_SENT,
                        'error_message' => null,
                    ]
                );

                $sent[] = [
                    'user_id' => $user->id,
                    'name' => $user->display_name ?: $user->name,
                    'mobile' => $user->email,
                    'response' => $response,
                ];

                $notifiedUsers[] = [
                    'user_id' => $user->id,
                    'name' => $user->display_name ?: $user->name,
                    'mobile' => $user->email,
                    'status' => $log->status,
                    'error' => null,
                    'logged_at' => $log->updated_at ? $log->updated_at->toDateTimeString() : null,
                ];
            } catch (Throwable $exception) {
                $log = DailyReportReminderLog::updateOrCreate(
                    [
                        'report_date' => $targetDate->toDateString(),
                        'user_id' => $user->id,
                    ],
                    [
                        'mobile' => $user->email,
                        'status' => DailyReportReminderLog::STATUS_FAILED,
                        'error_message' => $exception->getMessage(),
                    ]
                );

                $failed[] = [
                    'user_id' => $user->id,
                    'name' => $user->display_name ?: $user->name,
                    'mobile' => $user->email,
                    'error' => $exception->getMessage(),
                ];

                $notifiedUsers[] = [
                    'user_id' => $user->id,
                    'name' => $user->display_name ?: $user->name,
                    'mobile' => $user->email,
                    'status' => $log->status,
                    'error' => $log->error_message,
                    'logged_at' => $log->updated_at ? $log->updated_at->toDateTimeString() : null,
                ];
            }
        }

        return response()->json([
            'status' => 'ok',
            'date' => $targetDate->toDateString(),
            'template_id' => (int) $templateId,
            'total_users' => $users->count(),
            'reporting_user_ids' => $reportingUsers->values(),
            'daily_leave_user_ids' => $dailyLeaveUsers,
            'notified_user_ids' => $usersToNotify->pluck('id')->values(),
            'sent_count' => count($sent),
            'sent' => $sent,
            'failed_count' => count($failed),
            'failed' => $failed,
            'notified_users' => $notifiedUsers,
        ]);
    }

    private function getUsersWithReports(Carbon $date): Collection
    {
        return $this->reminderService->getUsersWithReports($date);
    }
}

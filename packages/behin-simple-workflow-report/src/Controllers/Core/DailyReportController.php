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
use Behin\Sms\Controllers\SmsController;
use BehinUserRoles\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Throwable;

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
            ->orderBy('case_number', 'desc')->get();

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
            ->orderBy('case_number', 'desc')->get();

        return view('SimpleWorkflowReportView::Core.DailyReport.show-mapa-center', compact('items'));
    }

    public function showExternalAsAssistant($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $from ? convertPersianToEnglish($from) : null;
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
            $startDate = convertPersianToEnglish($row->start_date);
            $startTime = convertPersianToEnglish($row->start_time);
            $startTime = str_replace('/', ':', $startTime);
            $row->start = Jalalian::fromFormat('Y-m-d H:i', "$startDate $startTime")->toCarbon()->timestamp;

            $endDate = convertPersianToEnglish($row->end_date);
            $endTime = convertPersianToEnglish($row->end_time);
            $endTime = str_replace('/', ':', $endTime);
            $row->end = Jalalian::fromFormat('Y-m-d H:i', "$endDate $endTime")->toCarbon()->timestamp;

            $row->duration = ((int)$row->end - (int)$row->start) / 3600; //به ساعت
        });

        return view('SimpleWorkflowReportView::Core.DailyReport.show-external-as-assistant', compact('items'));
    }

    public function showOtherDailyReport($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $from ? convertPersianToEnglish($from) : null;
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

        $templateId = config('services.sms.daily_report_reminder_template_id');
        if (! $templateId) {
            return response()->json([
                'status' => 'error',
                'message' => 'قالب پیامک یادآوری گزارش روزانه تعریف نشده است.',
            ], 422);
        }

        $parameterKey = config('services.sms.daily_report_reminder_parameter_key', 'NAME');

        $reportingUsers = $this->getUsersWithReports($targetDate);
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

        $users = User::query()->orderBy('number', 'asc')->get();

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
        $dateString = $date->toDateString();

        $reportingUsers = collect();

        $reportingUsers = $reportingUsers->merge(
            Part_reports::query()->whereDate('updated_at', $dateString)->pluck('registered_by')
        );

        $externalReports = Repair_reports::query()
            ->whereDate('created_at', $dateString)
            ->get(['mapa_expert', 'mapa_expert_companions']);

        $reportingUsers = $reportingUsers->merge(
            $externalReports->pluck('mapa_expert')->filter()
        );

        $assistantUsers = $externalReports->pluck('mapa_expert_companions')
            ->filter()
            ->flatMap(function ($companions) {
                return $this->extractCompanionIds($companions);
            });

        $reportingUsers = $reportingUsers->merge($assistantUsers);

        $reportingUsers = $reportingUsers->merge(
            Mapa_center_fix_report::query()->whereDate('updated_at', $dateString)->pluck('expert')
        );

        $reportingUsers = $reportingUsers->merge(
            $this->getOtherDailyReportUserIds($date)
        );

        return $reportingUsers
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values();
    }

    private function extractCompanionIds($companions): array
    {
        if ($companions instanceof Collection) {
            $companions = $companions->all();
        }

        if (is_array($companions)) {
            return $this->normalizeCompanionValues($companions);
        }

        if (is_string($companions)) {
            $decoded = json_decode($companions, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->normalizeCompanionValues($decoded);
            }

            $cleaned = str_replace(['[', ']', '"', "'"], '', $companions);
            $parts = preg_split('/[\\s,]+/', $cleaned);

            return $this->normalizeCompanionValues($parts ?: []);
        }

        return [];
    }

    private function normalizeCompanionValues(array $values): array
    {
        return collect($values)
            ->map(function ($value) {
                if (is_array($value) && array_key_exists('id', $value)) {
                    return $value['id'];
                }

                if (is_object($value) && isset($value->id)) {
                    return $value->id;
                }

                return $value;
            })
            ->filter(function ($value) {
                return is_numeric($value);
            })
            ->map(function ($value) {
                return (int) $value;
            })
            ->unique()
            ->values()
            ->all();
    }

    private function getOtherDailyReportUserIds(Carbon $date): Collection
    {
        $dateString = $date->toDateString();

        try {
            return Other_daily_reports::query()
                ->whereDate('created_at', $dateString)
                ->pluck('created_by');
        } catch (Throwable $exception) {
            try {
                return DB::table('wf_entity_other_daily_reports')
                    ->whereDate('created_at', $dateString)
                    ->pluck('created_by');
            } catch (Throwable $innerException) {
                return collect();
            }
        }
    }
}

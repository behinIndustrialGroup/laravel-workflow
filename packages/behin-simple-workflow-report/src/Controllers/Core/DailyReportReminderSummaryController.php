<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflowReport\Models\DailyReportReminderLog;
use Behin\SimpleWorkflowReport\Services\DailyReportReminderService;
use BehinUserRoles\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Morilog\Jalali\Jalalian;

class DailyReportReminderSummaryController extends Controller
{
    public function __construct(private DailyReportReminderService $reminderService)
    {
    }

    public function index(Request $request): View
    {
        $defaultFrom = Jalalian::now()->format('Y-m-d');
        $fromInput = convertPersianToEnglish($request->input('from_date', $defaultFrom));
        $toSource = $request->input('to_date', $request->input('from_date', $defaultFrom));
        $toInput = convertPersianToEnglish($toSource);

        $from = Jalalian::fromFormat('Y-m-d', $fromInput)->toCarbon()->startOfDay();
        $to = Jalalian::fromFormat('Y-m-d', $toInput)->toCarbon()->endOfDay();

        $allUsers = User::query()->orderBy('number', 'asc')->get();

        $users = $allUsers;
        if ($request->filled('user_id')) {
            $users = $allUsers
                ->where('id', (int) $request->input('user_id'))
                ->values();
        }

        $reminderLogs = $this->reminderService->getReminderLogs($from, $to, $request->integer('user_id'))
            ->groupBy('user_id');

        $uniqueDates = $reminderLogs
            ->flatMap(function (Collection $logs) {
                return $logs->pluck('report_date');
            })
            ->unique();

        $reportingUsersByDate = $uniqueDates->isEmpty()
            ? collect()
            : $this->reminderService->getReportingUsersByDate($uniqueDates);

        $users->each(function (User $user) use ($reminderLogs, $reportingUsersByDate) {
            /** @var Collection<int, DailyReportReminderLog> $logs */
            $logs = $reminderLogs->get($user->id, collect());
            $user->reminder_count = $logs->count();

            $userId = (int) $user->id;
            $user->missing_report_count = $logs->filter(function (DailyReportReminderLog $log) use ($reportingUsersByDate, $userId) {
                $dateString = Carbon::parse($log->report_date)->toDateString();
                $reportingUsers = $reportingUsersByDate->get($dateString, collect());

                return ! $reportingUsers->contains($userId);
            })->count();
        });

        $fromDate = Jalalian::fromCarbon($from)->format('Y-m-d');
        $toDate = Jalalian::fromCarbon($to)->format('Y-m-d');

        return view('SimpleWorkflowReportView::Core.DailyReportReminder.index', [
            'users' => $users,
            'allUsers' => $allUsers,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedUserId' => $request->input('user_id'),
        ]);
    }
}

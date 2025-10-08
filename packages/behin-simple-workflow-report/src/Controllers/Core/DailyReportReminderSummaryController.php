<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflowReport\Models\DailyReportReminderLog;
use Behin\SimpleWorkflowReport\Models\RewardPenalty;
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

        $rewardPenaltyRecords = RewardPenalty::query()
            ->select(['id', 'user_id', 'type', 'description', 'amount', 'created_at'])
            ->whereBetween('created_at', [$from, $to])
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->integer('user_id'));
            })
            ->get()
            ->groupBy('user_id');

        $rewardCounts = [];
        $penaltyCounts = [];
        $rewardDetails = [];
        $penaltyDetails = [];

        foreach ($rewardPenaltyRecords as $userId => $items) {
            $rewardItems = $items
                ->where('type', RewardPenalty::TYPE_REWARD)
                ->values();

            if ($rewardItems->isNotEmpty()) {
                $rewardCounts[$userId] = $rewardItems->count();
                $rewardDetails[$userId] = $rewardItems
                    ->map(function (RewardPenalty $record) {
                        return [
                            'description' => $record->description,
                            'amount' => $record->amount,
                            'formatted_amount' => number_format($record->amount),
                            'recorded_at' => Jalalian::fromCarbon($record->created_at)->format('Y-m-d'),
                        ];
                    })
                    ->values()
                    ->all();
            }

            $penaltyItems = $items
                ->where('type', RewardPenalty::TYPE_PENALTY)
                ->values();

            if ($penaltyItems->isNotEmpty()) {
                $penaltyCounts[$userId] = $penaltyItems->count();
                $penaltyDetails[$userId] = $penaltyItems
                    ->map(function (RewardPenalty $record) {
                        return [
                            'description' => $record->description,
                            'amount' => $record->amount,
                            'formatted_amount' => number_format($record->amount),
                            'recorded_at' => Jalalian::fromCarbon($record->created_at)->format('Y-m-d'),
                        ];
                    })
                    ->values()
                    ->all();
            }
        }

        $uniqueDates = $reminderLogs
            ->flatMap(function (Collection $logs) {
                return $logs->pluck('report_date');
            })
            ->unique();

        $reportingUsersByDate = $uniqueDates->isEmpty()
            ? collect()
            : $this->reminderService->getReportingUsersByDate($uniqueDates);

        $users->each(function (User $user) use ($reminderLogs, $reportingUsersByDate, $rewardCounts, $penaltyCounts) {
            /** @var Collection<int, DailyReportReminderLog> $logs */
            $logs = $reminderLogs->get($user->id, collect());
            $user->reminder_count = $logs->count();

            $userId = (int) $user->id;
            $user->missing_report_count = $logs->filter(function (DailyReportReminderLog $log) use ($reportingUsersByDate, $userId) {
                $dateString = Carbon::parse($log->report_date)->toDateString();
                $reportingUsers = $reportingUsersByDate->get($dateString, collect());

                return ! $reportingUsers->contains($userId);
            })->count();

            $user->reward_misc_count = $rewardCounts[$user->id] ?? 0;
            $user->penalty_misc_count = $penaltyCounts[$user->id] ?? 0;
        });

        $fromDate = Jalalian::fromCarbon($from)->format('Y-m-d');
        $toDate = Jalalian::fromCarbon($to)->format('Y-m-d');

        return view('SimpleWorkflowReportView::Core.DailyReportReminder.index', [
            'users' => $users,
            'allUsers' => $allUsers,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedUserId' => $request->input('user_id'),
            'rewardDetailsByUser' => $rewardDetails,
            'penaltyDetailsByUser' => $penaltyDetails,
        ]);
    }
}

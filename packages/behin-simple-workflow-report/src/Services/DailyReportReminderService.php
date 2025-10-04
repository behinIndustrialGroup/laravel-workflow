<?php

namespace Behin\SimpleWorkflowReport\Services;

use Behin\SimpleWorkflow\Models\Entities\Mapa_center_fix_report;
use Behin\SimpleWorkflow\Models\Entities\Other_daily_reports;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflowReport\Models\DailyReportReminderLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class DailyReportReminderService
{
    /**
     * Retrieve all user identifiers that have registered any type of daily report on the given date.
     */
    public function getUsersWithReports(Carbon $date): Collection
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

    /**
     * Determine how many reminder SMS logs (with sent status) exist for each user within the given period.
     */
    public function getReminderLogs(Carbon $from, Carbon $to, ?int $userId = null): Collection
    {
        $query = DailyReportReminderLog::query()
            ->where('status', DailyReportReminderLog::STATUS_SENT)
            ->whereBetween('report_date', [
                $from->toDateString(),
                $to->toDateString(),
            ]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('report_date')->get();
    }

    /**
     * Build a map of date string => collection of user IDs that have registered a report on that date.
     */
    public function getReportingUsersByDate(Collection $dates): Collection
    {
        $dates = $dates
            ->map(function ($date) {
                if ($date instanceof Carbon) {
                    return $date->copy()->startOfDay();
                }

                return Carbon::parse($date)->startOfDay();
            })
            ->unique(function (Carbon $date) {
                return $date->toDateString();
            })
            ->values();

        return $dates->mapWithKeys(function (Carbon $date) {
            return [
                $date->toDateString() => $this->getUsersWithReports($date),
            ];
        });
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

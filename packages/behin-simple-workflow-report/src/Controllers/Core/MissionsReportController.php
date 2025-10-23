<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Case_misson;
use Behin\SimpleWorkflow\Models\Entities\Missions;
use Behin\SimpleWorkflowReport\Exports\MissionsReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Morilog\Jalali\Jalalian;

class MissionsReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->prepareMissionsData($request);

        return view('SimpleWorkflowReportView::Core.Missions.index', $data);
    }

    public function export(Request $request)
    {
        $data = $this->prepareMissionsData($request);

        /** @var Collection $missions */
        $missions = $data['missions'];

        $from = str_replace('-', '', $data['fromValue'] ?? '');
        $to = str_replace('-', '', $data['toValue'] ?? '');
        $fileName = 'missions-report.xlsx';
        if ($from || $to) {
            $fileName = sprintf('missions-report-%s-%s.xlsx', $from, $to);
        }

        return (new MissionsReportExport($missions))->download($fileName);
    }

    /**
     * @return array{
     *     missions: Collection<int, mixed>,
     *     monthOptions: array<int, array<string, string>>,
     *     selectedMonth: string,
     *     fromValue: string,
     *     toValue: string
     * }
     */
    private function prepareMissionsData(Request $request): array
    {
        $query = Missions::query();

        $monthOptions = [];
        $currentMonth = Jalalian::now();
        for ($i = 0; $i < 12; $i++) {
            $month = clone $currentMonth;
            if ($i > 0) {
                $month = $month->subMonths($i);
            }

            $monthOptions[] = [
                'value' => $month->format('Y-m'),
                'label' => $month->format('%B %Y'),
                'from' => $month->getFirstDayOfMonth()->format('Y-m-d'),
                'to' => $month->getEndDayOfMonth()->format('Y-m-d'),
            ];
        }

        $selectedMonthInput = $request->input('month', $monthOptions[0]['value']);
        $selectedMonth = convertPersianToEnglish($selectedMonthInput);
        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = $monthOptions[0]['value'];
        } else {
            [, $selectedMonthNumber] = array_map('intval', explode('-', $selectedMonth));
            if ($selectedMonthNumber < 1 || $selectedMonthNumber > 12) {
                $selectedMonth = $monthOptions[0]['value'];
            }
        }

        $selectedMonthJalali = Jalalian::fromFormat('Y-m-d', $selectedMonth . '-01');
        $defaultFrom = $selectedMonthJalali->getFirstDayOfMonth()->format('Y-m-d');
        $defaultTo = $selectedMonthJalali->getEndDayOfMonth()->format('Y-m-d');

        $fromValue = $defaultFrom;
        $toValue = $defaultTo;

        if ($request->filled('from_date')) {
            $candidate = convertPersianToEnglish($request->input('from_date'));
            if ($this->isValidJalaliDate($candidate)) {
                $fromValue = $candidate;
            }
        }

        if ($request->filled('to_date')) {
            $candidate = convertPersianToEnglish($request->input('to_date'));
            if ($this->isValidJalaliDate($candidate)) {
                $toValue = $candidate;
            }
        }

        $fromCarbon = $this->jalaliToCarbonStartOfDay($fromValue);
        $toCarbon = $this->jalaliToCarbonEndOfDay($toValue);

        if ($fromCarbon) {
            $query->where('start_datetime_alt', '>=', $fromCarbon->valueOf());
        }

        if ($toCarbon) {
            $query->where('start_datetime_alt', '<=', $toCarbon->valueOf());
        }

        $missions = $query->orderByDesc('start_datetime_alt')
            ->get()
            ->map(function ($mission) {
                $startAlt = $mission->start_datetime_alt ? (int) $mission->start_datetime_alt : null;
                $endAlt = $mission->end_datetime_alt ? (int) $mission->end_datetime_alt : null;

                if ($startAlt !== null && $endAlt !== null && $endAlt >= $startAlt) {
                    $durationMilliseconds = $endAlt - $startAlt;
                    $durationSeconds = $durationMilliseconds / 1000;
                    $mission->duration_hours = $durationSeconds / 3600;
                } else {
                    $mission->duration_hours = null;
                }

                $mission->start_datetime_carbon = $startAlt !== null
                    ? Carbon::createFromTimestampMs($startAlt, config('app.timezone'))
                    : null;
                $mission->end_datetime_carbon = $endAlt !== null
                    ? Carbon::createFromTimestampMs($endAlt, config('app.timezone'))
                    : null;

                $mission->cases = Case_misson::where('case_number', $mission->case_number)->get();

                return $mission;
            });

        return [
            'missions' => $missions,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'fromValue' => $fromValue,
            'toValue' => $toValue,
        ];
    }

    private function jalaliToCarbonStartOfDay(?string $date): ?Carbon
    {
        if (!$date) {
            return null;
        }

        try {
            return Jalalian::fromFormat('Y-m-d', $date)->toCarbon()->startOfDay();
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function jalaliToCarbonEndOfDay(?string $date): ?Carbon
    {
        if (!$date) {
            return null;
        }

        try {
            return Jalalian::fromFormat('Y-m-d', $date)->toCarbon()->endOfDay();
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function isValidJalaliDate(?string $date): bool
    {
        if (!$date) {
            return false;
        }

        try {
            Jalalian::fromFormat('Y-m-d', $date);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}

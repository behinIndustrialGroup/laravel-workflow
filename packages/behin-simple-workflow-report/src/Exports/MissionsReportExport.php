<?php

namespace Behin\SimpleWorkflowReport\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MissionsReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    public function __construct(private readonly Collection $missions)
    {
    }

    public function collection(): Collection
    {
        return $this->missions;
    }

    public function headings(): array
    {
        return [
            'عنوان مأموریت',
            'ایجادکننده',
            'تاریخ شروع',
            'تاریخ پایان',
            'مدت (ساعت)',
            'پرونده‌ها',
        ];
    }

    public function map($mission): array
    {
        if (is_array($mission)) {
            $mission = (object) $mission;
        }

        $title = $mission->title ?? '';

        $startCarbon = $mission->start_datetime_carbon ?? null;
        $start = $startCarbon ? toJalali($startCarbon)->format('Y-m-d H:i') : '';

        $endCarbon = $mission->end_datetime_carbon ?? null;
        $end = $endCarbon ? toJalali($endCarbon)->format('Y-m-d H:i') : '';

        $creatorInfo = getUserInfo($mission->created_by ?? null);
        $creator = $creatorInfo ? ($creatorInfo->name ?? $creatorInfo->full_name ?? '-') : '-';

        $duration = '-';
        if (isset($mission->duration_hours) && $mission->duration_hours !== null) {
            $duration = number_format((float) $mission->duration_hours, 2);
        }

        $cases = collect($mission->cases ?? [])
            ->pluck('related_case_number')
            ->filter()
            ->implode(' - ');

        return [
            $title,
            $creator,
            $start,
            $end,
            $duration,
            $cases,
        ];
    }
}

<?php

namespace Behin\SimpleWorkflowReport\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyReportReminderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<int|string, array<int, array<string, mixed>>>  $rewardDetailsByUser
     * @param  array<int|string, array<int, array<string, mixed>>>  $penaltyDetailsByUser
     */
    public function __construct(
        private readonly Collection $rows,
        private readonly array $rewardDetailsByUser,
        private readonly array $penaltyDetailsByUser
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'شماره',
            'نام',
            'تعداد پیامک‌های یادآوری',
            'تعداد روزهای بدون گزارش پس از یادآوری',
            'پاداش‌های متفرقه',
            'جرایم متفرقه',
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function map($row): array
    {
        $number = $row['number'] ?? null;
        if ($number === null || $number === '') {
            $number = $row['row_number'];
        }

        $rewardDetails = $this->formatDetails($this->rewardDetailsByUser[$row['user_id']] ?? []);
        $penaltyDetails = $this->formatDetails($this->penaltyDetailsByUser[$row['user_id']] ?? []);

        return [
            $number,
            $row['name'],
            $row['reminder_count'],
            $row['missing_report_count'],
            $rewardDetails,
            $penaltyDetails,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function formatDetails(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        return collect($items)
            ->map(function (array $item): string {
                $amount = $item['formatted_amount'] ?? number_format((float) ($item['amount'] ?? 0));
                $description = $item['description'] ?? '';
                $recordedAt = $item['recorded_at'] ?? '';

                return sprintf('توضیح: %s; مبلغ: %s; تاریخ: %s', $description, $amount, $recordedAt);
            })
            ->implode(PHP_EOL);
    }
}

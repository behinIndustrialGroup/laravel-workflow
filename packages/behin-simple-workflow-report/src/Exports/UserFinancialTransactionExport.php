<?php

namespace Behin\SimpleWorkflowReport\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserFinancialTransactionExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    public function __construct(private readonly Collection $creditors)
    {
    }

    public function collection(): Collection
    {
        return $this->creditors;
    }

    public function headings(): array
    {
        return [
            'نام پرسنل',
            'مانده حساب',
        ];
    }

    public function map($creditor): array
    {
        $name = $creditor?->name ?? '';
        $balance = $creditor?->total_amount ?? 0;

        return [
            $name,
            $balance,
        ];
    }
}

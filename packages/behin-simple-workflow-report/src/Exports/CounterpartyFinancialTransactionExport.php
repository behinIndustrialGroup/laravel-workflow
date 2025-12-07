<?php

namespace Behin\SimpleWorkflowReport\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CounterpartyFinancialTransactionExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function __construct(private readonly Collection $financialTransaction)
    {
    }

    public function collection(): Collection
    {
        return $this->financialTransaction;
    }

    public function headings(): array
    {
        return [
           'نوع',
            'طرف حساب',
            'مبلغ',
            'بابت پرونده',
            'نوع پرداختی',
            'شماره چک/شماره تراکنش',
            'تاریخ سررسید چک/تاریخ تراکنش',
            'نام مفصد حساب',
            'شماره مقصد حساب',
            'توضیحات',
        ];
    }

}

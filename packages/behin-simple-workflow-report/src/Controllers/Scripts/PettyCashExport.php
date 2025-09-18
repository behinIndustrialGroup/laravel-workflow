<?php

namespace Behin\SimpleWorkflowReport\Controllers\Scripts;

use Behin\SimpleWorkflowReport\Models\PettyCash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Morilog\Jalali\Jalalian;

class PettyCashExport implements FromCollection, WithHeadings, WithStyles
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = PettyCash::query();
        if ($this->from) {
            $from = convertPersianToEnglish($this->from);
            $from = Jalalian::fromFormat('Y-m-d', $from)
                ->toCarbon()
                ->startOfDay()
                ->timestamp;
            $query->where('paid_at', '>=', $from);
        }
        if ($this->to) {
            $to = convertPersianToEnglish($this->to);
            $to = Jalalian::fromFormat('Y-m-d', $to)
                ->toCarbon()
                ->endOfDay()
                ->timestamp;
            $query->where('paid_at', '<=', $to);
        }
        return $query->select('title','amount','paid_at','from_account')->get()->map(function($item){
            return [
                $item->title,
                $item->amount,
                Jalalian::forge($item->paid_at)->format('Y-m-d'),
                $item->from_account,
            ];
        });
    }

    public function headings(): array
    {
        return ['عنوان خرج', 'مبلغ', 'تاریخ پرداخت', 'از حساب'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        return [1 => ['font' => ['bold' => true]]];
    }
}


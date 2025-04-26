<?php

namespace Behin\SimpleWorkflowReport\Controllers\Scripts;

use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Morilog\Jalali\Jalalian;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Behin\SimpleWorkflowReport\Controllers\Core\TimeoffController;

class UserTimeoffs implements FromCollection, WithHeadings, WithStyles
{
    public $userId;
    public function __construct($userId =null)
    {
        $this->userId = $userId;
    }
    public function collection()
    {
        $items = TimeoffController::items($this->userId);

        $ar = [];
        $duration = 0;
        foreach ($items as $item) {
            if ($item->type == 'ساعتی' and $item->approved == 1) {
                $duration += $item->duration;
            } elseif ($item->type == 'روزانه' and $item->approved == 1) {
                $duration += $item->duration * 8;
            }
            $ar[] = [
                $item->user()?->number,
                $item->user()?->name,
                $item->type,
                toJalali((int)$item->start_timestamp)->format('Y-m-d H:i'),
                toJalali((int)$item->end_timestamp)->format('Y-m-d H:i'),
                $item->approved ? 'تایید شده' : 'تایید نشده',
                $item->description
            ];
        }

        $ar[] = ['', '', '', 'مجموع', $duration, '', '', ''];
        return collect($ar);
    }

    public function headings(): array
    {
        return [
            'شماره پرسنلی',
            'نام',
            'نوع',
            'شروع',
            'پایان',
            'تایید',
            'توضیحات'
        ];
    }

    // اضافه کردن استایل برای راست به چپ
    public function styles(Worksheet $sheet)
    {
        // تنظیم راست به چپ برای کل سلول‌ها
        $sheet->setRightToLeft(true);
        $sheet->getColumnDimension('A')->setWidth(10); // ستون شماره پرسنلی
        $sheet->getColumnDimension('B')->setWidth(20); // ستون نام
        $sheet->getColumnDimension('C')->setWidth(10); // ستون نوع
        $sheet->getColumnDimension('D')->setWidth(20); // ستون شروع
        $sheet->getColumnDimension('E')->setWidth(20); // ستون پایان
        $sheet->getColumnDimension('F')->setWidth(20); // ستون تایید مدیر دپارتمان
        $sheet->getColumnDimension('G')->setWidth(30); // ستون توضیحات مدیر دپارتمان
        $sheet->getStyle('G')->getAlignment()->setWrapText(true);
        // تنظیم استایل سرستون‌ها و سایر سلول‌ها
        return [
            1    => ['font' => ['bold' => true]], // بولد کردن سرستون‌ها
        ];
    }
}

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Scripts;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Morilog\Jalali\Jalalian;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimeoffExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $today = Carbon::today();
        $todayShamsi = Jalalian::fromCarbon($today);
        $thisMonth = $todayShamsi->getMonth();
        $totalLeaves = $thisMonth * 20; // مقدار کل مرخصی‌ها بر اساس ماه جاری

        return DB::table('wf_entity_timeoffs')
            ->select(
                'users.number as user_number', // شماره پرسنلی
                'users.name as user_name', // نام کاربر
                'wf_entity_timeoffs.request_year',
                'wf_entity_timeoffs.request_month',
                DB::raw("ROUND(($totalLeaves) - SUM(CASE WHEN type = 'ساعتی' THEN duration ELSE duration*8 END), 1) as remaining_leaves") // گرد کردن مانده مرخصی
            )
            ->join('users', 'wf_entity_timeoffs.user', '=', 'users.id')
            ->where('approved', 1)
            ->groupBy('wf_entity_timeoffs.user', 'wf_entity_timeoffs.request_year', 'wf_entity_timeoffs.request_month')
            ->orderBy('wf_entity_timeoffs.request_year', 'desc')
            ->orderBy('wf_entity_timeoffs.request_month', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'شماره پرسنلی',
            'نام',
            'سال',
            'ماه',
            'مانده مرخصی',
        ];
    }

    // اضافه کردن استایل برای راست به چپ
    public function styles(Worksheet $sheet)
    {
        // تنظیم راست به چپ برای کل سلول‌ها
        $sheet->setRightToLeft(true);

        // تنظیم استایل سرستون‌ها و سایر سلول‌ها
        return [
            1    => ['font' => ['bold' => true]], // بولد کردن سرستون‌ها
        ];
    }
}

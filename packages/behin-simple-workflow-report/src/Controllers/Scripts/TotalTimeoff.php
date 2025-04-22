<?php

namespace Behin\SimpleWorkflowReport\Controllers\Scripts;

use Behin\SimpleWorkflowReport\Controllers\Core\TimeoffController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Morilog\Jalali\Jalalian;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TotalTimeoff implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $today = Carbon::today();
        $todayShamsi = Jalalian::fromCarbon($today);
        $thisYear = $todayShamsi->getYear();
        $thisMonth = $todayShamsi->getMonth();
        $totalLeaves = $thisMonth * 20; // مقدار کل مرخصی‌ها بر اساس ماه جاری
        $users = DB::table('users')->get();
        $users = TimeoffController::totalLeaves();
        $ar = [];
        foreach ($users as $user) {
            $ar[] = [
                'user_number' => $user->number,
                'user_name' => $user->name,
                'request_year' => $thisYear,
                'remaining_leaves' => $user->restLeaves
            ];
        }
        return collect($ar);
    }

    public function headings(): array
    {
        return [
            'شماره پرسنلی',
            'نام',
            'سال',
            'مانده مرخصی',
        ];
    }

    // اضافه کردن استایل برای راست به چپ
    public function styles(Worksheet $sheet)
    {
        // تنظیم راست به چپ برای کل سلول‌ها
        $sheet->setRightToLeft(true);
        $sheet->getColumnDimension('A')->setWidth(10); // ستون شماره پرسنلی
        $sheet->getColumnDimension('B')->setWidth(20); // ستون نام
        $sheet->getColumnDimension('C')->setWidth(5); // ستون نوع
        $sheet->getColumnDimension('D')->setWidth(10); // ستون شروع
        $sheet->getColumnDimension('E')->setWidth(10); // ستون پایان
        // تنظیم استایل سرستون‌ها و سایر سلول‌ها
        return [
            1    => ['font' => ['bold' => true]], // بولد کردن سرستون‌ها
        ];
    }
}

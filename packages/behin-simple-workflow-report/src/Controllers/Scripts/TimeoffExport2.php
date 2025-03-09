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

class TimeoffExport2 implements FromCollection, WithHeadings, WithStyles
{
    public $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
    }
    public function collection()
    {
        $today = Carbon::today();
        $todayShamsi = Jalalian::fromCarbon($today);
        $thisMonth = $todayShamsi->getMonth();
        $totalLeaves = $thisMonth * 20; // مقدار کل مرخصی‌ها بر اساس ماه جاری
        $process = ProcessController::getById("211ed341-c06c-41cb-881c-d33e8d4cd905");
        $thisMonthLeaves = [];
        $hourlyLeaves = [];
        if ($this->userId) {
            foreach ($process->cases as $case) {
                $type = $case->getVariable('timeoff_request_type');
                $department_manager = $case->getVariable('department_manager');
                $user_department_manager_approval = $case->getVariable('user_department_manager_approval');
                $start_date = $case->getVariable('timeoff_hourly_request_start_date');
                $start_date = convertPersianToEnglish($start_date);
                if ($type === 'ساعتی' && $department_manager && $user_department_manager_approval && $case->creator == $this->userId) {
                    $startMonth = Jalalian::fromFormat('Y-m-d', $start_date)->format('%m');
                    if ($thisMonth == $startMonth) {
                        $hourlyLeaves[] = [
                            getUserInfo($case->creator)->number,
                            getUserInfo($case->creator)->name,
                            $type,
                            $start_date . ' - ' . $case->getVariable('timeoff_start_time'),
                            $start_date . ' - ' . $case->getVariable('timeoff_end_time'),
                            // $case->getVariable('timeoff_hourly_request_duration'),
                        ];
                    }
                }


                if ($type === 'روزانه' && $department_manager && $user_department_manager_approval && $case->creator == $this->userId) {
                    $start_date = convertPersianToEnglish($case->getVariable('timeoff_start_date'));
                    $startMonth = Jalalian::fromFormat('Y-m-d', $start_date)->format('%m');
                    if ($thisMonth == $startMonth) {
                        // $duration = $case->getVariable('timeoff_daily_request_duration');
                        $thisMonthLeaves[] = [
                            getUserInfo($case->creator)->number,
                            getUserInfo($case->creator)->name,
                            $type,
                            $start_date,
                            $start_date,
                            // $duration,
                        ];
                    }
                }
            }
        }else{
            foreach ($process->cases as $case) {
                $type = $case->getVariable('timeoff_request_type');
                $department_manager = $case->getVariable('department_manager');
                $user_department_manager_approval = $case->getVariable('user_department_manager_approval');
                $start_date = $case->getVariable('timeoff_hourly_request_start_date');
                $start_date = convertPersianToEnglish($start_date);
                if ($type === 'ساعتی' && $department_manager && $user_department_manager_approval) {
                    $gregorianStartDate = Jalalian::fromFormat('Y-m-d', $start_date)
                        ->toCarbon()
                        ->format('Y-m-d');
                    $diff = $today->diffInDays($gregorianStartDate);
                    if ($diff >= 0) {
                        $hourlyLeaves[] = [
                            getUserInfo($case->creator)->number,
                            getUserInfo($case->creator)->name,
                            $type,
                            $start_date . ' - ' . $case->getVariable('timeoff_start_time'),
                            $start_date . ' - ' . $case->getVariable('timeoff_end_time'),
                            // $case->getVariable('timeoff_hourly_request_duration'),
                        ];
                    }
                }
    
    
                if ($type === 'روزانه' && $department_manager && $user_department_manager_approval) {
                    $start_date = convertPersianToEnglish($case->getVariable('timeoff_start_date'));
                    $gregorianStartDate = Jalalian::fromFormat('Y-m-d', $start_date)
                        ->toCarbon()
                        ->format('Y-m-d');
                    $diff = $today->diffInDays($gregorianStartDate);
                    if ($diff >= 0) {
                        // $duration = $case->getVariable('timeoff_daily_request_duration');
                        $thisMonthLeaves[] = [
                            getUserInfo($case->creator)->number,
                            getUserInfo($case->creator)->name,
                            $type,
                            $start_date,
                            $start_date,
                            // $duration,
                        ];
                    }
                }
            }
        }
        $merged = collect(array_merge($hourlyLeaves, $thisMonthLeaves))->sortBy(function ($item) {
            // استخراج تاریخ و ساعت از متن
            $dateTimeParts = explode(' - ', $item[3]);
            $persianDate = $dateTimeParts[0];
            $time = $dateTimeParts[1];
        
            // تبدیل تاریخ شمسی به میلادی (اگر از کتابخانه `morilog/jalali` استفاده می‌کنید)
            $gregorianDate = \Morilog\Jalali\CalendarUtils::toGregorian(
                substr($persianDate, 0, 4), // سال
                substr($persianDate, 5, 2), // ماه
                substr($persianDate, 8, 2)  // روز
            );
        
            // ایجاد یک شیء Carbon برای مرتب‌سازی
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i', implode('-', $gregorianDate) . ' ' . $time)->timestamp;
        });
        return $merged;
    }

    public function headings(): array
    {
        return [
            'شماره پرسنلی',
            'نام',
            'نوع',
            'شروع',
            'پایان',
            // 'مدت',
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
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 40,
            'D' => 80,
            'E' => 80,
        ];
    }
}

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
        $merged = array_merge($hourlyLeaves, $thisMonthLeaves);
        return collect($merged)->sortBy('start_date');
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
}

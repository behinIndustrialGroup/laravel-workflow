<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Behin\SimpleWorkflow\Models\Entities\Holidays;
use Morilog\Jalali\Jalalian;


class CreateTimeAndDateOfTimeoffRequest extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        // بررسی ساعت مجاز برای ثبت درخواست
        $now = Carbon::now();
        $currentHour = $now->hour;
    
        if ($currentHour < 8 || $currentHour >= 17) {
            return "ثبت درخواست فقط بین ساعت 8 صبح تا 17 مجاز است.";
        }
        $today = Carbon::today()->format('Y-m-d');
        $todayShamsi = toJalali(Carbon::today());
        $holiday = Holidays::where('date', $today)->first();
        if($holiday){
            return "امروز تعطیل است. شما نمیتوانید در روز تعطیل مرخصی ثبت کنید";
        }
        $variables = $this->case->variables();
        $type = $case->getVariable('timeoff_request_type');
        if($type == 'ساعتی'){
            $start = $case->getVariable('timeoff_start_time');
            $end = $case->getVariable('timeoff_end_time');
            $date = $case->getVariable('timeoff_hourly_request_start_date');
            
            if(!$start){
                return "زمان شروع اجباریست";
            }
            if(!$end){
                return "زمان پایان اجباریست";
            }
            if(!$date){
                return "تاریخ شروع اجباریست";
            }
            
            $date= convertPersianToEnglish($date);
            
            $start = str_pad($start, 5, '0', STR_PAD_LEFT);
            $end = str_pad($end, 5, '0', STR_PAD_LEFT);
            
            $startTimeStamp = Jalalian::fromFormat('Y-m-d H:i', "$date $start")->toCarbon()->timestamp;
            
            
            $now = Carbon::now()->subMinutes(15)->timestamp;
            // return Carbon::now();
            if( $startTimeStamp < $now ){
                return "تاریخ و ساعت شروع برای زمان گذشته است. شما نمیتوانید برای تاریخ گذشته مرخصی ثبت کنید";
            }
            
            $endTimeStamp = Jalalian::fromFormat('Y-m-d H:i', "$date $end")->toCarbon()->timestamp;
            
            
            $duration = ($endTimeStamp - $startTimeStamp) /3600;
            if($duration < 0){
                return "ساعت پایان باید از ساعت شروع بیشتر باشد";
            }
            $startDate = explode('-', $date);
            $endDate = explode('-', $date);
            
            Timeoffs::updateOrCreate([
                'uniqueId' => $case->id
                ],
                [
                'case_id' => $case->id,
                'case_number' => $case->number,
                'user' => $this->case->creator,
                'type' => $type,
                'duration' => $duration,
                'approved' => 0,
                'start_timestamp' => $startTimeStamp,
                'end_timestamp' => $endTimeStamp,
                'reuqest_timestamp' => Carbon::now()->timestamp
            ]);
            
        }else{
            $date = $case->getVariable('timeoff_start_date');
            if(!$date){
                return "تاریخ شروع وارد نشده است";
            }
            
            $date = convertPersianToEnglish($date);
            $startDateShamsi = Jalalian::fromFormat('Y-m-d', $date);
            $startDate = $startDateShamsi->toCarbon();
            $startDateTimestamp = $startDate->timestamp;
            // $requestDate = $startDateShamsi->toCarbon();
            $requestDateTimestamp = Jalalian::fromFormat('Y-m-d', $date)->toCarbon()->timestamp;
            $now = Carbon::now();
            $today = Carbon::today();
            
            if ($startDate->lt($today)) {
                return "تاریخ شروع برای زمان گذشته است. شما نمیتوانید برای تاریخ گذشته مرخصی ثبت کنید";
            }
            
            // if ($requestDate->isSameDay($today)) {
            //     if ($now->hour >= 12) {
            //         return "مهلت ثبت مرخصی برای امروز تا ساعت ۱۲ بود.";
            //     }
            // }
            $endDate = $case->getVariable('timeoff_end_date');
            if(!$endDate){
                return "تاریخ پایان وارد نشده است";
            }
            $endDate= convertPersianToEnglish($endDate);
            $endDateShamsi = Jalalian::fromFormat('Y-m-d', $endDate);
            $endDate = $endDateShamsi->toCarbon();
            $endDateTimestamp = $endDate->timestamp;
            
            if($endDateTimestamp < $startDateTimestamp){
                return "تاریخ شروع از تاریخ پایان بزرگتر است";
            }
            
            if($endDateShamsi->getMonth() != $startDateShamsi->getMonth()){
                return "ماه شروع مرخصی و ماه پایان مرخصی باید مساوی باشند. مرخصی های هرماه را به صورت جداگانه ثبت کنید";
            }
            
            $startDate = explode('-', $date);
            $endDate = explode('-', $endDate);
            $request = new Request([
                'timeoff_start_date' => $this->case->getVariable('timeoff_start_date'),
                'timeoff_end_date' => $this->case->getVariable('timeoff_end_date')
                ]);
            $duration = CalculateDailyTimeoffDuration::execute($request);
            if(!is_numeric($duration)){
                return "خطا در محاسبه مدت زمان مرخصی، با پشتیبانی تماس بگیرید";
            }
            VariableController::save(
                $this->case->process->id,     
                $this->case->id,
                'timeoff_daily_request_duration',
                $duration
            );
            
            Timeoffs::updateOrCreate([
                'uniqueId' => $case->id
                ],
                [
                'case_id' => $case->id,
                'case_number' => $case->number,
                'user' => $this->case->creator,
                'type' => $type,
                'duration' => $duration,
                'approved' => 0,
                'start_timestamp' => $startDateTimestamp,
                'end_timestamp' => $endDateTimestamp,
                'reuqest_timestamp' => Carbon::now()->timestamp
            ]);
            
        }
        
        $now = Carbon::now();
        $nowTimestamp = $now->timestamp;
        $now = toJalali($now);
        $requestDate = explode('-', $now);
        
        $case->saveVariable('timeoff_uniqueId', $case->id);
        VariableController::save(
            $this->case->process->id,     
            $this->case->id,
            'creator',
            $this->case->creator
        );
        VariableController::save(
            $this->case->process->id,     
            $this->case->id,
            'timeoff_register_datetime',
            $now
        );
        VariableController::save(
            $this->case->process->id,     
            $this->case->id,
            'timeoff_register_date_timestamp',
            $nowTimestamp
        );
        
        
        
    }
}
<?php

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\PushNotifications;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Behin\SimpleWorkflowReport\Controllers\Core\ExternalAndInternalReportController;
use BehinInit\App\Http\Middleware\Access;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Mkhodroo\AgencyInfo\Controllers\GetAgencyController;
use Pusher\Pusher;
use UserProfile\Controllers\ChangePasswordController;
use UserProfile\Controllers\GetUserAgenciesController;
use UserProfile\Controllers\NationalIdController;
use UserProfile\Controllers\UserProfileController;
use Illuminate\Support\Facades\Http;
use Morilog\Jalali\Jalalian;

Route::get('', function(){
    return view('auth.login');
});

require __DIR__.'/auth.php';

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', Access::class])->group(function(){
    Route::get('', function(){
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::get('/pusher/beams-auth', function (Request $request) {
    $beamsClient = new PushNotifications([
        'instanceId' => config('broadcasting.pusher.instanceId'),
        'secretKey' => config('broadcasting.pusher.secretKey')
    ]);
    $userId = auth()->user()->id;
    $beamsToken = $beamsClient->generateToken('user-mobile-'.$userId);
    // $user = User::find($userId);
    return response()->json($beamsToken);
})->middleware('auth');

Route::get('send-notification', function () {
    SendPushNotification::dispatch(Auth::user()->id, 'test', 'test', route('admin.dashboard'));
    return 'تا دقایقی دیگر باید نوتیفیکیشن دریافت کنید';
})->name('send-notification');

Route::get('queue-work', function () {
    $limit = 5; // تعداد jobهای پردازش شده در هر درخواست
    $jobs = DB::table('jobs')->orderBy('id')->limit($limit)->get();

    foreach ($jobs as $job) {
        try {
            // دیکد کردن محتوای job
            $payload = json_decode($job->payload, true);
            $command = unserialize($payload['data']['command']);

            // اجرای job
            $command->handle();

            // حذف job پس از اجرا
            DB::table('jobs')->where('id', $job->id)->delete();

            // return 'Job processed: ' . $job->id;
        } catch (Exception $e) {
            // در صورت خطا، job را به جدول failed_jobs منتقل کنید
            DB::table('failed_jobs')->insert([
                'connection' => $job->connection,
                'queue' => $job->queue,
                'payload' => $job->payload,
                'exception' => (string) $e,
                'failed_at' => now()
            ]);

            DB::table('jobs')->where('id', $job->id)->delete();

            return 'Job failed: ' . $e->getMessage();
        }
    }
});

Route::get('build-app', function(){
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('migrate');
    return redirect()->back();
});

Route::get('test', function(){
    $cases = Variable::where('key', 'timeoff_request_type')->where('value', 'ساعتی')->pluck('case_id');
    foreach($cases as $caseId){
        $case = CaseController::getById($caseId);
        if($case){
            $type = $case->getVariable('timeoff_request_type');
            if($type == 'ساعتی'){
                $startDate = $case->getVariable('timeoff_hourly_request_start_date');
                $startDate = convertPersianToEnglish($startDate);
                if(strlen($startDate) == 10){
                    $startTime = $case->getVariable('timeoff_start_time');
                    $startTime = str_pad($startTime, 5, '0', STR_PAD_LEFT);
                    $gregorianStartDate = Jalalian::fromFormat('Y-m-d H:i', "$startDate $startTime")->toCarbon()->timestamp;
                    $endTime = $case->getVariable('timeoff_end_time');
                    $endTime = str_pad($endTime, 5, '0', STR_PAD_LEFT);
                    $gregorianEndDate = Jalalian::fromFormat('Y-m-d H:i', "$startDate $endTime")->toCarbon()->timestamp;
                    echo Carbon::createFromTimestamp($gregorianEndDate, 'Asia/Tehran') . "\t $endTime <br>";
                    $case->saveVariable('start_timestamp', $gregorianStartDate);
                    $case->saveVariable('end_timestamp', $gregorianEndDate);
                }
            }
        }
    }
});

Route::get('test2', function(){
    $timeoffs = Timeoffs::whereIn('request_month', ['01', '02'])->whereNot('uniqueId', 'به صورت دستی')->get();
    $processId = "211ed341-c06c-41cb-881c-d33e8d4cd905";
    foreach($timeoffs as $t){
        $t->request_timestamp = $t->created_at->timestamp;
        $t->save();
        if($t->type == 'ساعتی'){
            $t->start_timestamp = '';
            $uniqueId = $t->uniqueId;
            $var = Variable::where('key', 'timeoff_uniqueId')->where('value', $uniqueId)->first();
            if($var){
                $caseId = $var->case_id;
                $case = CaseController::getById($caseId);
                if($case){
                    $start = $case->getVariable('timeoff_start_time');
                    $end = $case->getVariable('timeoff_end_time');
                    $timeoff_hourly_request_start_date = $case->getVariable('timeoff_hourly_request_start_date');
                    $startDate = convertPersianToEnglish($timeoff_hourly_request_start_date);
                    $start = str_pad($start, 5, '0', STR_PAD_LEFT);
                    $end = str_pad($end, 5, '0', STR_PAD_LEFT);
                    $startTimeStamp = Jalalian::fromFormat('Y-m-d H:i', "$startDate $start")->toCarbon()->timestamp;
                    $endTimeStamp = Jalalian::fromFormat('Y-m-d H:i', "$startDate $end")->toCarbon()->timestamp;
                    // $s = Carbon::createFromTimestamp($startTimeStamp, 'Asia/Tehran');
                    // echo $caseId . ' ### ' . $startTimeStamp .' ### ' . $s . '<br>';
                    $t->start_timestamp = $startTimeStamp;
                    $t->end_timestamp = $endTimeStamp;
                    $t->save();
                }
                
            }
        }
        if($t->type == 'روزانه'){
            $t->start_timestamp = '';
            $uniqueId = $t->uniqueId;
            $var = Variable::where('key', 'timeoff_uniqueId')->where('value', $uniqueId)->first();
            if($var){
                $caseId = $var->case_id;
                $case = CaseController::getById($caseId);
                if($case){
                    $start = $case->getVariable('timeoff_start_date');
                    $end = $case->getVariable('timeoff_end_date');
                    $startDate = convertPersianToEnglish($start);
                    $endDate = convertPersianToEnglish($end);
                    $startTimeStamp = Jalalian::fromFormat('Y-m-d', "$startDate")->toCarbon()->timestamp;
                    $endTimeStamp = Jalalian::fromFormat('Y-m-d', "$endDate")->toCarbon()->timestamp;
                    // $s = Carbon::createFromTimestamp($startTimeStamp, 'Asia/Tehran');
                    // echo $caseId . ' ### ' . $startTimeStamp .' ### ' . $s . '<br>';
                    $t->start_timestamp = $startTimeStamp;
                    $t->end_timestamp = $endTimeStamp;
                    $t->save();
                }
                
            }
        }
    }
});

Route::get('test3', function(){
    $cases = Cases::whereIn('process_id', [
        '35a5c023-5e85-409e-8ba4-a8c00291561c',
        '4bb6287b-9ddc-4737-9573-72071654b9de',
        '1763ab09-1b90-4609-af45-ef5b68cf10d0',
    ])
        ->whereNull('parent_id')
        ->whereNotNull('number')
        ->groupBy('number')
        ->get()
        ->filter(function ($case) {
            $whereIsResult = $case->whereIs();
            return !($whereIsResult[0]?->archive == 'yes');
        });

    foreach($cases as $case){
        try{
            ExternalAndInternalReportController::show($case->number);
        }catch(Exception $e){
            echo $case->number . ' ### ' . $e->getMessage() . '<br>';
        }
    }
});



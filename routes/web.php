<?php

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\PushNotifications;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Variable;
use BehinInit\App\Http\Middleware\Access;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

                }
                
            }
        }
    }
});



<?php

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\PushNotifications;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Customers;
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
use Maatwebsite\Excel\Facades\Excel;
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
                'connection' => $job->connection ?? 'database',
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

Route::get('test', function () {
    $financials = Financials::all()->map(function ($financial) {
        $customerVar = Variable::where('case_id', $financial->case_id)
            ->where('key', 'customer_workshop_or_ceo_name')
            ->first();

        $financial->customer = $customerVar ? $customerVar->value : null;
        return $financial;
    });

    // گروه‌بندی بر اساس customer
    $grouped = $financials->groupBy('customer');
    dd($grouped);
    return response()->json($grouped);
});



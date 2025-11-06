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
use Behin\SimpleWorkflow\Models\Entities\OnCreditPayment;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;


Route::get('', function () {
    return view('auth.login');
});

require __DIR__ . '/auth.php';

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', Access::class])->group(function () {
    Route::get('', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::get('/pusher/beams-auth', function (Request $request) {
    $beamsClient = new PushNotifications([
        'instanceId' => config('broadcasting.pusher.instanceId'),
        'secretKey' => config('broadcasting.pusher.secretKey')
    ]);
    $userId = auth()->user()->id;
    $beamsToken = $beamsClient->generateToken('user-mobile-' . $userId);
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

Route::get('build-app', function () {
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


Route::get('test2', function () {
    $onCredits = DB::table('wf_entity_financials')
        ->join('wf_cases', 'wf_cases.id', '=', 'wf_entity_financials.case_id')
        ->leftJoin('wf_variables as v_customer', function ($join) {
            $join->on('v_customer.case_id', '=', 'wf_cases.id')
                ->where('v_customer.key', '=', 'customer_workshop_or_ceo_name');
        })
        ->whereNotNull('wf_entity_financials.case_number')
        ->whereIn('wf_entity_financials.fix_cost_type', ['حساب دفتری'])
        ->whereNull('wf_entity_financials.is_passed')
        ->whereNull('wf_entity_financials.deleted_at')
        ->whereNull('wf_entity_financials.counter_party_id')
        ->select(
            'wf_entity_financials.id',
            'wf_entity_financials.case_number',
            'wf_entity_financials.fix_cost_type',
            'wf_entity_financials.cost',
            'wf_entity_financials.counter_party_id',
            'v_customer.value as customer_name'
        )
        ->groupBy('customer_name')->get();
    $counterParties = Counter_parties::all();
    return view('test', compact('onCredits','counterParties'));
    foreach($onCredits as $onCredit){
        echo"<form action='/test3' method='post'>";
        echo csrf_field();
        echo "<input type='text' name='customer_name' value='".$onCredit->customer_name."'>";
        echo "<select name='counter_party_id'>";
        echo "<option value=''></option>";
        foreach($counterParties as $counterParty){
            $select = $counterParty->id == $onCredit->counter_party_id ? 'selected' : '';
            echo "<option value='".$counterParty->id."' $select>".$counterParty->name."</option>";
        }
        echo "</select>";
        echo "<input type='submit'>";
        echo"</form><br>";
    }
    return ;
    return response()->json($onCredits);
});

Route::post('test3', function (Request $request) {
    $customerName = $request->customer_name;
    $counterPartyId = $request->counter_party_id;

    // مرحله ۱: پیدا کردن case_id های مربوط به این مشتری
    $caseIds = DB::table('wf_variables')
        ->where('key', 'customer_workshop_or_ceo_name')
        ->where('value', $customerName)
        ->pluck('case_id');

    // dd($caseIds);

    if ($caseIds->isEmpty()) {
        return "موردی برای '{$customerName}' پیدا نشد.";
    }

    // مرحله ۲: آپدیت همه رکوردهای مالی مرتبط
    $fins = DB::table('wf_entity_financials')
        ->whereIn('case_id', $caseIds)
        ->update(['counter_party_id' => $counterPartyId]);
    return redirect()->back()->with('success', "طرف حساب مشتری '{$customerName}' با موفقیت به‌روزرسانی شد.");
});


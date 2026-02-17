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
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflow\Models\Entities\Missions;
use Behin\SimpleWorkflowReport\Controllers\Core\FinancialTransactionController;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Creditor;
use Behin\SimpleWorkflowReport\Controllers\Core\CounterPartyController;

Route::get('', function () {
    return view('auth.login');
});

require __DIR__ . '/auth.php';

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
    Route::get('', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('warehouse-dashboard', function () {
        return view('admin.warehouse');
    })->name('warehouseDashboard');
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

// Route::get('test', function () {
//     $financials = Financials::all()->map(function ($financial) {
//         $customerVar = Variable::where('case_id', $financial->case_id)
//             ->where('key', 'customer_workshop_or_ceo_name')
//             ->first();

//         $financial->customer = $customerVar ? $customerVar->value : null;
//         return $financial;
//     });

//     // گروه‌بندی بر اساس customer
//     $grouped = $financials->groupBy('customer');
//     dd($grouped);
//     return response()->json($grouped);
// });


// Route::get('test2', function () {
//     $onCredits = DB::table('wf_entity_financials')
//         // ->join('wf_cases', 'wf_cases.id', '=', 'wf_entity_financials.case_id')
//         // ->leftJoin('wf_variables as v_customer', function ($join) {
//         //     $join->on('v_customer.case_id', '=', 'wf_cases.id')
//         //         ->where('v_customer.key', '=', 'customer_workshop_or_ceo_name');
//         // })
//         ->whereNotNull('wf_entity_financials.case_number')
//         ->whereIn('wf_entity_financials.fix_cost_type', ['حساب دفتری'])
//         // ->whereNull('wf_entity_financials.is_passed')
//         ->whereNull('wf_entity_financials.deleted_at')
//         ->select(
//             'wf_entity_financials.id',
//             'wf_entity_financials.case_number',
//             'wf_entity_financials.case_id',
//             'wf_entity_financials.fix_cost_type',
//             'wf_entity_financials.cost',
//             'wf_entity_financials.is_passed',
//             'wf_entity_financials.counter_party_id',
//             // 'v_customer.value as customer_name',
//             'wf_entity_financials.destination_account',
//             'wf_entity_financials.destination_account_name',
//             'wf_entity_financials.description',
//             'wf_entity_financials.invoice_number',
//             'wf_entity_financials.cheque_number',
//             'wf_entity_financials.cheque_due_date',
//             'wf_entity_financials.payment_date',
//             'wf_entity_financials.payment',
//         )
//         ->get();
//     foreach($onCredits as $onCredit){
//         $onCredit->payments = OnCreditPayment::where('case_number', $onCredit->case_number)->get();
//     }

//     echo "<table>";
//     $loop = 1;
//     foreach($onCredits as $onCredit){
//         if($onCredit->counter_party_id and $onCredit->payments->count() > 0 and $onCredit->is_passed){
//             if($onCredit->cost == $onCredit->payments->sum('amount')){
//                 // $ft1 = Financial_transactions::create([
//                 //     'case_number' => $onCredit->case_number,
//                 //     'case_id' => $onCredit->case_id,
//                 //     'financial_type' => 'بدهکار',
//                 //     'counterparty_id' => $onCredit->counter_party_id,
//                 //     'amount' => $onCredit->cost,
//                 //     'description' => 'از حساب دفتری '.  $onCredit->description,
//                 // ]);
//                 // $ft2 = Financial_transactions::create([
//                 //     'case_number' => $onCredit->case_number,
//                 //     'case_id' => $onCredit->case_id,
//                 //     'financial_type' => 'بستانکار',
//                 //     'financial_method' => $onCredit->fix_cost_type,
//                 //     'counterparty_id' => $onCredit->counter_party_id,
//                 //     'amount' => $onCredit->payments->sum('amount'),
//                 //     'description' => 'از حساب دفتری '.  $onCredit->description,
//                 //     'invoice_or_cheque_number' => $onCredit->cheque_number ? $onCredit->cheque_number : $onCredit->invoice_number,
//                 //     'transaction_or_cheque_due_date' => $onCredit->cheque_due_date ? $onCredit->cheque_due_date : $onCredit->payment_date,
//                 //     'destination_account_name' => $onCredit->destination_account_name,
//                 //     'destination_account_number' => $onCredit->destination_account,
//                 // ]);
//                 echo "<tr>";
//                 echo "<td>".$loop."</td>";
//                 echo "<td>".$onCredit->case_number."</td>";
//                 echo "<td>".$onCredit->cost."</td>";
//                 echo "<td>";
//                 if($onCredit->is_passed){
//                     echo "بله";
//                 }else{
//                     echo "نه";
//                 }
//                 echo "</td>";
//                 echo "<td>" . $onCredit->counter_party_id . "</td>";
//                 foreach($onCredit->payments as $payment){
//                     echo "<td>".$payment->amount."</td>";
//                 }
//                 echo "<td>converted";
//                 echo "</td>";
//                 echo "</tr>";
//                 DB::table('wf_entity_financials')->where('id', $onCredit->id)->update(['converted' => 1]);
//                 $loop++;
//             }
//         }
//     }
//     echo "</table>";
//     return ;
//     $counterParties = Counter_parties::all();

//     return view('test', compact('onCredits','counterParties'));
//     foreach($onCredits as $onCredit){
//         echo"<form action='/test3' method='post'>";
//         echo csrf_field();
//         echo "<input type='text' name='customer_name' value='".$onCredit->customer_name."'>";
//         echo "<select name='counter_party_id'>";
//         echo "<option value=''></option>";
//         foreach($counterParties as $counterParty){
//             $select = $counterParty->id == $onCredit->counter_party_id ? 'selected' : '';
//             echo "<option value='".$counterParty->id."' $select>".$counterParty->name."</option>";
//         }
//         echo "</select>";
//         echo "<input type='submit'>";
//         echo"</form><br>";
//     }
//     return ;
//     return response()->json($onCredits);
// });

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


// Route::get('test4', function () {
//     $onCredits = DB::table('wf_entity_financials')
//         // ->join('wf_cases', 'wf_cases.id', '=', 'wf_entity_financials.case_id')
//         // ->leftJoin('wf_variables as v_customer', function ($join) {
//         //     $join->on('v_customer.case_id', '=', 'wf_cases.id')
//         //         ->where('v_customer.key', '=', 'customer_workshop_or_ceo_name');
//         // })
//         ->whereNotNull('wf_entity_financials.case_number')
//         ->whereIn('wf_entity_financials.fix_cost_type', ['حساب دفتری'])
//         // ->whereNull('wf_entity_financials.is_passed')
//         ->whereNull('wf_entity_financials.deleted_at')
//         ->select(
//             'wf_entity_financials.id',
//             'wf_entity_financials.case_number',
//             'wf_entity_financials.case_id',
//             'wf_entity_financials.fix_cost_type',
//             'wf_entity_financials.cost',
//             'wf_entity_financials.is_passed',
//             'wf_entity_financials.counter_party_id',
//             'wf_entity_financials.destination_account',
//             'wf_entity_financials.destination_account_name',
//             'wf_entity_financials.description',
//             'wf_entity_financials.invoice_number',
//             'wf_entity_financials.cheque_number',
//             'wf_entity_financials.cheque_due_date',
//             'wf_entity_financials.payment_date',
//             'wf_entity_financials.payment',
//         )
//         ->get();
//     foreach($onCredits as $onCredit){
//         $onCredit->payments = OnCreditPayment::where('case_number', $onCredit->case_number)->get();
//     }

//     echo "<table>";
//     $loop = 1;
//     foreach($onCredits as $onCredit){
//         if($onCredit->counter_party_id and $onCredit->payments->count() == 0 and !$onCredit->is_passed){
//                 // $ft1 = Financial_transactions::create([
//                 //     'case_number' => $onCredit->case_number,
//                 //     'case_id' => $onCredit->case_id,
//                 //     'financial_type' => 'بدهکار',
//                 //     'counterparty_id' => $onCredit->counter_party_id,
//                 //     'amount' => $onCredit->cost,
//                 //     'description' => 'از حساب دفتری '.  $onCredit->description,
//                 // ]);
//                 echo "<tr>";
//                 echo "<td>".$loop."</td>";
//                 echo "<td>".$onCredit->case_number."</td>";
//                 echo "<td>".$onCredit->cost."</td>";
//                 echo "<td>";
//                 if($onCredit->is_passed){
//                     echo "بله";
//                 }else{
//                     echo "نه";
//                 }
//                 echo "</td>";
//                 echo "<td>" . $onCredit->counter_party_id . "</td>";
//                 foreach($onCredit->payments as $payment){
//                     echo "<td>".$payment->amount."</td>";
//                 }
//                 echo "</tr>";
//                 DB::table('wf_entity_financials')->where('id', $onCredit->id)->update(['converted' => 1]);
//                 $loop++;
//         }
//     }
//     echo "</table>";
//     return ;
// });

// Route::get('test5', function () {
//     $onCredits = DB::table('wf_entity_financials')
//         ->join('wf_cases', 'wf_cases.id', '=', 'wf_entity_financials.case_id')
//         ->leftJoin('wf_variables as v_customer', function ($join) {
//             $join->on('v_customer.case_id', '=', 'wf_cases.id')
//                 ->where('v_customer.key', '=', 'customer_workshop_or_ceo_name');
//         })
//         ->whereNotNull('wf_entity_financials.case_number')
//         ->whereIn('wf_entity_financials.fix_cost_type', ['حساب دفتری'])
//         // ->whereNull('wf_entity_financials.is_passed')
//         ->whereNull('wf_entity_financials.deleted_at')
//         ->select(
//             'wf_entity_financials.id',
//             'wf_entity_financials.case_number',
//             'wf_entity_financials.fix_cost_type',
//             'wf_entity_financials.cost',
//             'wf_entity_financials.is_passed',
//             'wf_entity_financials.counter_party_id',
//             'v_customer.value as customer_name'
//         )
//         ->get();
//     foreach($onCredits as $onCredit){
//         $onCredit->payments = OnCreditPayment::where('case_number', $onCredit->case_number)->get();
//     }

//     echo "<table>";
//     $loop = 1;
//     foreach($onCredits as $onCredit){
//         if($onCredit->counter_party_id and $onCredit->payments->count() == 0 and $onCredit->is_passed){
//                 echo "<tr>";
//                 echo "<td>".$loop."</td>";
//                 echo "<td>".$onCredit->case_number."</td>";
//                 echo "<td>".$onCredit->cost."</td>";
//                 echo "<td>";
//                 if($onCredit->is_passed){
//                     echo "بله";
//                 }else{
//                     echo "نه";
//                 }
//                 echo "</td>";
//                 echo "<td>" . $onCredit->counter_party_id . "</td>";
//                 foreach($onCredit->payments as $payment){
//                     echo "<td>".$payment->amount."</td>";
//                 }
//                 echo "</tr>";
//                 $loop++;
//         }
//     }
//     echo "</table>";
//     return ;
// });

// Route::get('test6', function () {
//     $onCredits = DB::table('wf_entity_financials')
//         ->join('wf_cases', 'wf_cases.id', '=', 'wf_entity_financials.case_id')
//         ->leftJoin('wf_variables as v_customer', function ($join) {
//             $join->on('v_customer.case_id', '=', 'wf_cases.id')
//                 ->where('v_customer.key', '=', 'customer_workshop_or_ceo_name');
//         })
//         ->whereNotNull('wf_entity_financials.case_number')
//         ->whereIn('wf_entity_financials.fix_cost_type', ['حساب دفتری'])
//         // ->whereNull('wf_entity_financials.is_passed')
//         ->whereNull('wf_entity_financials.deleted_at')
//         ->select(
//             'wf_entity_financials.id',
//             'wf_entity_financials.case_number',
//             'wf_entity_financials.fix_cost_type',
//             'wf_entity_financials.cost',
//             'wf_entity_financials.is_passed',
//             'wf_entity_financials.counter_party_id',
//             'v_customer.value as customer_name'
//         )
//         ->get();
//     foreach($onCredits as $onCredit){
//         $onCredit->payments = OnCreditPayment::where('case_number', $onCredit->case_number)->get();
//     }

//     echo "<table>";
//     $loop = 1;
//     foreach($onCredits as $onCredit){
//         if($onCredit->counter_party_id and $onCredit->payments->count() > 0 and !$onCredit->is_passed){
//                 echo "<tr>";
//                 echo "<td>".$loop."</td>";
//                 echo "<td>".$onCredit->case_number."</td>";
//                 echo "<td>".$onCredit->cost."</td>";
//                 echo "<td>";
//                 if($onCredit->is_passed){
//                     echo "بله";
//                 }else{
//                     echo "نه";
//                 }
//                 echo "</td>";
//                 echo "<td>" . $onCredit->counter_party_id . "</td>";
//                 foreach($onCredit->payments as $payment){
//                     echo "<td>".$payment->amount."</td>";
//                 }
//                 echo "</tr>";
//                 $loop++;
//         }
//     }
//     echo "</table>";
//     return ;
// });

// Route::get('test7', function () {
//     $onCredits = DB::table('wf_entity_financials')
//         ->join('wf_cases', 'wf_cases.id', '=', 'wf_entity_financials.case_id')
//         ->leftJoin('wf_variables as v_customer', function ($join) {
//             $join->on('v_customer.case_id', '=', 'wf_cases.id')
//                 ->where('v_customer.key', '=', 'customer_workshop_or_ceo_name');
//         })
//         ->whereNotNull('wf_entity_financials.case_number')
//         ->whereIn('wf_entity_financials.fix_cost_type', ['حساب دفتری'])
//         // ->whereNull('wf_entity_financials.is_passed')
//         ->whereNull('wf_entity_financials.deleted_at')
//         ->select(
//             'wf_entity_financials.id',
//             'wf_entity_financials.case_number',
//             'wf_entity_financials.fix_cost_type',
//             'wf_entity_financials.cost',
//             'wf_entity_financials.is_passed',
//             'wf_entity_financials.counter_party_id',
//             'v_customer.value as customer_name'
//         )
//         ->get();
//     foreach($onCredits as $onCredit){
//         $onCredit->payments = OnCreditPayment::where('case_number', $onCredit->case_number)->get();
//     }

//     echo "<table>";
//     $loop = 1;
//     foreach($onCredits as $onCredit){
//         if(!$onCredit->counter_party_id){
//                 echo "<tr>";
//                 echo "<td>".$loop."</td>";
//                 echo "<td>".$onCredit->case_number."</td>";
//                 echo "<td>".$onCredit->cost."</td>";
//                 echo "<td>";
//                 if($onCredit->is_passed){
//                     echo "بله";
//                 }else{
//                     echo "نه";
//                 }
//                 echo "</td>";
//                 echo "<td>" . $onCredit->counter_party_id . "</td>";
//                 foreach($onCredit->payments as $payment){
//                     echo "<td>".$payment->amount."</td>";
//                 }
//                 echo "</tr>";
//                 $loop++;
//         }
//     }
//     echo "</table>";
//     return ;
// });

Route::get('test8', function () {
    $fins = Financials::query();
    $fins->where('counter_party_id', '!=', null);
    $fins->where('fix_cost_type', '!=', 'حساب دفتری');
    $fins->get();
    return $fins->count();
});

Route::get('test9', function () {
    $fins = Financials::query();
    $fins->where('counter_party_id', 'cya5xKXLoJ');
    $fins->where('fix_cost_type', '!=', 'حساب دفتری');
    $fins = $fins->paginate(25);
    foreach ($fins as $fin) {
        $fin->case = Cases::find($fin->case_id);
    }
    $counterParties = Counter_parties::all();
    return view('test', compact('fins', 'counterParties'));
});

Route::get('test10', function () {
    $fins = Financials::query();
    $fins->whereNotNull('counter_party_id');
    $fins->where('fix_cost_type', '!=', 'حساب دفتری');
    $fins->whereNull('converted');
    // $fins->where('case_number', 4322);
    $fins = $fins->orderBy('case_number', 'desc')->paginate(25);
    $fins->getCollection()->transform(function ($item) {
        $item->counterparty = Counter_parties::find($item->counter_party_id);
        return $item;
    });
    foreach ($fins as $fin) {
        $fin->case = Cases::find($fin->case_id);
    }
    $counterParties = Counter_parties::all();
    return view('test10', compact('fins', 'counterParties'));
});

Route::post('test11', function (Request $request) {

    $fins = Financials::query();
    $fins->whereNotNull('counter_party_id');
    $fins->where('fix_cost_type', '!=', 'حساب دفتری');
    $fins->where('id', $request->id);
    $fins->whereNull('converted');
    $fins = $fins->first();

    if ($fins) {
        if (str_contains($fins->payment_method, 'نقدی') or $fins->payment_method == null) {
            $debit = array(
                'case_id' => $fins->case_id,
                'case_number' => $fins->case_number,
                'financial_type' => 'بدهکار',
                'counterparty_id' => $fins->counter_party_id,
                'financial_method' => 'نقدی',
                'amount' => $request->amount,
                'description' => $fins->fix_cost_type . ' ' . $fins->description,
                'transaction_or_cheque_due_date' => '',
                'transaction_or_cheque_due_date_alt,' => $fins->fix_cost_date,
                'invoice_or_cheque_number' => $fins->invoice_number,
                'destination_account_name' => '',
                'destination_account_number' => '',
            );

            $credit = array(
                'case_id' => $fins->case_id,
                'case_number' => $fins->case_number,
                'financial_type' => 'بستانکار',
                'amount' => $request->amount,
                'financial_method' => 'نقدی',
                'counterparty_id' => $fins->counter_party_id,
                'description' => $fins->fix_cost_type . ' ' . $fins->description,
                'invoice_or_cheque_number' => $fins->invoice_number,
                'transaction_or_cheque_due_date' => '',
                'transaction_or_cheque_due_date_alt,' => $fins->payment_date,
                'destination_account_name' => $fins->destination_account_name,
                'destination_account_number' => $fins->destination_account,
            );

            Financial_transactions::create($debit);
            Financial_transactions::create($credit);

            $fins->converted = 1;
            $fins->save();
            Log::info($fins->case_number);
            return redirect()->back();
            return [
                'debit' => $debit,
                'credit' => $credit
            ];
        }
    }
});

Route::get('test13', function () {
    $creditors = Creditor::get();
    DB::transaction(function () use ($creditors) {
        foreach ($creditors as $creditor) {
            $counterparty = CounterPartyController::getByName($creditor->counterparty);
            if (!$counterparty) {
                return $creditor->id;
            } else {
                if ($creditor->type == 'طلب') {
                    $financialType = 'بستانکار';
                } else {
                    $financialType = 'بدهکار';
                }
                $amount = str_replace(',', '', $creditor->amount);
                $amount = str_replace('-', '', $creditor->amount);
                // $amount = abs($amount);
                // Financial_transactions::create([
                //     'financial_type' => $financialType,
                //     'amount' => $amount,
                //     'financial_method' => $creditor->settlement_type,
                //     'counterparty_id' => $counterparty->id,
                //     'description' => 'از بخش طلبکاران - '. $creditor->description,
                //     'invoice_or_cheque_number' => $creditor->invoice_number,
                //     'transaction_or_cheque_due_date' => $creditor->invoice_date,
                // ]);
            }
        }
    });
});



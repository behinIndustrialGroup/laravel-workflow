<?php

use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FieldController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\RoutingController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskActorController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflowReport\Controllers\Core\ExpiredController;
use Behin\SimpleWorkflowReport\Controllers\Core\FinReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\ProcessController;
use Behin\SimpleWorkflowReport\Controllers\Core\ReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\RoleReportFormController;
use Behin\SimpleWorkflowReport\Controllers\Core\SummaryReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\TimeoffController;
use Behin\SimpleWorkflowReport\Controllers\Scripts\TotalTimeoff;
use Behin\SimpleWorkflowReport\Controllers\Scripts\UserTimeoffs;
use BehinProcessMaker\Models\PMVariable;
use BehinProcessMaker\Models\PmVars;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::name('simpleWorkflowReport.')->prefix('workflow-report')->middleware(['web', 'auth'])->group(function () {
    Route::get('index', [ReportController::class, 'index'])->name('index');
    Route::resource('report', ReportController::class);
    Route::resource('summary-report', SummaryReportController::class);
    Route::resource('role', RoleReportFormController::class);
    Route::resource('fin-report', FinReportController::class);
    Route::name('fin.')->prefix('fin')->group(function(){
        Route::get('', [FinReportController::class, 'index'])->name('index');
        Route::get('total-cost', [FinReportController::class, 'totalCost'])->name('totalCost');
        Route::get('all-payments/{year?}/{month?}/{user?}', [FinReportController::class, 'allPayments'])->name('allPayments');
    });
    Route::get('total-payment', [FinReportController::class, 'totalPayment'])->name('totalPayment');
    Route::get('total-timeoff', function(){
        return Excel::download(new TotalTimeoff, 'total_timeoff.xlsx');
    })->name('totalTimeoff');

    Route::get('user-timeoffs/{userId?}', function($userId = null){
        return Excel::download(new UserTimeoffs($userId), 'timeoff_report.xlsx');
    })->name('userTimeoffs');

    Route::post('timeoff/update', [TimeoffController::class, 'update'])->name('timeoff.update');

    Route::resource('expired-tasks', ExpiredController::class);

    Route::name('process.')->prefix('process')->group(function(){
        Route::prefix('{processId}')->group(function(){
            Route::post('update', [ProcessController::class, 'update'])->name('update');
            Route::get('export', [ProcessController::class, 'export'])->name('export');
        });
    });
    Route::get('test', function(){
        return DB::table('wf_entity_timeoffs')->whereColumn('start_day', '!=', 'end_day')->get();
    });
    // Route::get('import', function () {
    //     $cases = PmVars::groupBy('case_id')->get();
    //     foreach ($cases as $case) {
    //         $number = PmVars::where('case_id', $case->case_id)->where('key', 'case_number')->first()?->value;
    //         $number = $number ? $number : 1;
    //         $creator = PmVars::where('case_id', $case->case_id)->where('key', 'crm_user_creator')->first()?->value;
    //         $creator = $creator ? $creator : 1;
    //         $name = PmVars::where('case_id', $case->case_id)->where('key', 'device_serial_no')->first()?->value;
    //         $name = 'سریال نامبر: ' . $name;
    //         $newCase = Cases::create([
    //             'process_id' => '879e001c-59d5-4afb-958c-15ec7ff269d1',
    //             'number' => $number,
    //             'name' => $name,
    //             'creator' => $creator
    //         ]);
    //         $vars = PmVars::where('case_id', $case->case_id)->get()->each(function ($row) use ($newCase) {
    //             $row->process_id = '879e001c-59d5-4afb-958c-15ec7ff269d1';
    //             $row->case_id = $newCase->id;
    //         })->toArray();
    //         foreach ($vars as $var) {
    //             Variable::create($var);
    //         }
    //     }
    //     // $cases = Variable::where('key', 'case_number')->get();
    //     // foreach($cases as $case){
    //     //     $newCase = Cases::create([
    //     //         'process_id' => $case->process_id,
    //     //         'number' => $case->value ? $case->value : 1,
    //     //         'name' => '',
    //     //         'creator' => 1
    //     //     ]);
    //     //     Variable::where('case_id', $case->case_id)->update(['case_id' => $newCase->id]);
    //     // }
    // });
});

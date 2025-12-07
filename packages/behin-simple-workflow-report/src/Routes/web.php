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
use Behin\SimpleWorkflowReport\Controllers\Core\ChequeReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\ExpiredController;
use Behin\SimpleWorkflowReport\Controllers\Core\ExternalAndInternalReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\FinReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\GoodsInReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\MapaCenterController;
use Behin\SimpleWorkflowReport\Controllers\Core\MissionsReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\ProcessController;
use Behin\SimpleWorkflowReport\Controllers\Core\RewardPenaltyController;
use Behin\SimpleWorkflowReport\Controllers\Core\ReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\RoleReportFormController;
use Behin\SimpleWorkflowReport\Controllers\Core\SummaryReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\TimeoffController;
use Behin\SimpleWorkflowReport\Controllers\Core\CounterPartyController;
use Behin\SimpleWorkflowReport\Controllers\Core\CreditorReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\DailyReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\DailyReportReminderSummaryController;
use Behin\SimpleWorkflowReport\Controllers\Core\EmployeeSalaryReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\FinancialTransactionController;
use Behin\SimpleWorkflowReport\Controllers\Core\OnCreditReportController;
use Behin\SimpleWorkflowReport\Controllers\Core\PersonelActivityController;
use Behin\SimpleWorkflowReport\Controllers\Core\PettyCashController;
use Behin\SimpleWorkflowReport\Controllers\Core\PhonebookController;
use Behin\SimpleWorkflowReport\Controllers\Scripts\TotalTimeoff;
use Behin\SimpleWorkflowReport\Controllers\Scripts\UserTimeoffs;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::name('simpleWorkflowReport.')->prefix('workflow-report')->middleware(['web', 'auth'])->group(function () {
    Route::get('index', [ReportController::class, 'index'])->name('index');
    Route::resource('report', ReportController::class);
    Route::resource('summary-report', SummaryReportController::class);
    Route::resource('role', RoleReportFormController::class);
    Route::resource('fin-report', FinReportController::class);
    Route::resource('external-internal', ExternalAndInternalReportController::class);
    Route::post('external-internal/search', [ExternalAndInternalReportController::class, 'search'])->name('external-internal.search');
    Route::get('external-internal-archive', [ExternalAndInternalReportController::class, 'archive'])->name('external-internal-archive');

    Route::post('counter-party/merge', [CounterPartyController::class, 'merge'])->name('counter-party.merge');
    Route::resource('counter-party', CounterPartyController::class);
    Route::resource('phonebook', PhonebookController::class)->except(['show']);

    Route::get('goods-in', [GoodsInReportController::class, 'index'])->name('goods-in.index')->middleware('access:گزارش ورود کالاها در نگهبانی');
    Route::get('missions', [MissionsReportController::class, 'index'])->name('missions.index')->middleware('access:گزارش ماموریت');
    Route::get('missions/export', [MissionsReportController::class, 'export'])->name('missions.export')->middleware('access:گزارش ماموریت');
    Route::delete('missions/{mission}', [MissionsReportController::class, 'destroy'])->name('missions.destroy')->middleware('access:حذف ماموریت');


    Route::name('fin.')->prefix('fin')->middleware('access:گزارش درآمد تقریبی')->group(function(){
        Route::get('', [FinReportController::class, 'index'])->name('index');
        Route::get('total-cost', [FinReportController::class, 'totalCost'])->name('totalCost');
        Route::get('all-payments/{year?}/{month?}/{user?}', [FinReportController::class, 'allPayments'])->name('allPayments');
    });
    Route::get('total-payment', [FinReportController::class, 'totalPayment'])->name('totalPayment')->middleware('access:گزارش درآمد تقریبی');
    Route::get('total-timeoff', function(){
        return Excel::download(new TotalTimeoff, 'total_timeoff.xlsx');
    })->name('totalTimeoff');

    Route::get('user-timeoffs/{userId?}', function($userId = null){
        return Excel::download(new UserTimeoffs($userId), 'timeoff_report.xlsx');
    })->name('userTimeoffs');

    Route::post('timeoff/update', [TimeoffController::class, 'update'])->name('timeoff.update');

    Route::get('employee-salaries', [EmployeeSalaryReportController::class, 'index'])->name('employee-salaries.index')->middleware('access:گزارش لیست حقوق پرسنل');
    Route::post('employee-salaries', [EmployeeSalaryReportController::class, 'update'])->name('employee-salaries.update')->middleware('access:گزارش لیست حقوق پرسنل');

    Route::resource('expired-tasks', ExpiredController::class);

    Route::name('process.')->prefix('process')->group(function(){
        Route::prefix('{processId}')->group(function(){
            Route::post('update', [ProcessController::class, 'update'])->name('update');
            Route::get('export', [ProcessController::class, 'export'])->name('export');
        });
    });


    Route::resource('mapa-center', MapaCenterController::class);
    Route::get('mapa-center/archive-show/{mapa_center}', [MapaCenterController::class, 'archiveShow'])->name('mapa-center.archive-show');
    Route::put('mapa-center/update-case-info/{mapa_center}', [MapaCenterController::class, 'updateCaseInfo'])->name('mapa-center.update-case-info');
    Route::post('mapa-center/exclude-device/{mapa_center}', [MapaCenterController::class, 'excludeDevice'])->name('mapa-center.exclude-device');
    Route::post('mapa-center/install-part/{mapa_center}', [MapaCenterController::class, 'installPart'])->name('mapa-center.install-part');
    Route::get('mapa-center/delete-install-part/{id}', [MapaCenterController::class, 'deleteInstallPart'])->name('mapa-center.delete-install-part');
    Route::get('mapa-center-archive', [MapaCenterController::class, 'archive'])->name('mapa-center-archive');

    Route::resource('cheque-report', ChequeReportController::class)->middleware('access:گزارش چک ها');
    Route::patch('cheque-report/{id}/update-from-on-credit', [ChequeReportController::class, 'updateFromOnCredit'])->name('cheque-report.updateFromOnCredit')->middleware('access:گزارش چک ها');
    Route::resource('on-credit-report', OnCreditReportController::class)->middleware('access:گزارش حساب دفتری');
    Route::get('on-credit-report-show-all', [OnCreditReportController::class, 'showAll'])->name('on-credit-report.showAll')->middleware('access:گزارش حساب دفتری');
    Route::resource('petty-cash', PettyCashController::class)->except(['show', 'create'])->middleware('access:گزارش تنخواه');
    Route::get('petty-cash/export', [PettyCashController::class, 'export'])->name('petty-cash.export')->middleware('access:گزارش تنخواه');
    Route::resource('personel-activity', PersonelActivityController::class)->middleware('access:گزارش اقدامات پرسنل');
    Route::get('personel-activity/{user_id}/show-inboxes/{from?}/{to?}', [PersonelActivityController::class, 'showInboxes'])->name('personel-activity.showInboxes')->middleware('access:گزارش اقدامات پرسنل');
    Route::get('personel-activity/{user_id}/show-dones/{from?}/{to?}', [PersonelActivityController::class, 'showDones'])->name('personel-activity.showDones')->middleware('access:گزارش اقدامات پرسنل');

    Route::get('daily-report', [DailyReportController:: class, 'index'])->name('daily-report.index')->middleware('access:گزارش روزانه');
    Route::get('daily-report/reminder-summary', [DailyReportReminderSummaryController::class, 'index'])->name('daily-report.reminder-summary')->middleware('access:گزارش جرایم پیامک ها');
    Route::get('daily-report/reminder-summary/export', [DailyReportReminderSummaryController::class, 'export'])->name('daily-report.reminder-summary.export')->middleware('access:گزارش جرایم پیامک ها');
    Route::get('rewards-penalties', [RewardPenaltyController::class, 'index'])->name('rewards-penalties.index')->middleware('access:گزارش جرایم پیامک ها');
    Route::post('rewards-penalties', [RewardPenaltyController::class, 'store'])->name('rewards-penalties.store')->middleware('access:گزارش جرایم پیامک ها');
    Route::get('daily-report/{user_id}/show-internal/{from?}/{to?}', [DailyReportController:: class, 'showInternal'])->name('daily-report.show-internal')->middleware('access:گزارش روزانه');
    Route::get('daily-report/{user_id}/show-external/{from?}/{to?}', [DailyReportController:: class, 'showExternal'])->name('daily-report.show-external')->middleware('access:گزارش روزانه');
    Route::get('daily-report/{user_id}/show-mapa-center/{from?}/{to?}', [DailyReportController:: class, 'showMapaCenter'])->name('daily-report.show-mapa-center')->middleware('access:گزارش روزانه');
    Route::get('daily-report/{user_id}/show-external-as-assistant/{from?}/{to?}', [DailyReportController:: class, 'showExternalAsAssistant'])->name('daily-report.show-external-as-assistant')->middleware('access:گزارش روزانه');
    Route::get('daily-report/{user_id}/show-other-daily-report/{from?}/{to?}', [DailyReportController:: class, 'showOtherDailyReport'])->name('daily-report.show-other-daily-report')->middleware('access:گزارش روزانه');

    Route::resource('creditor', CreditorReportController::class)->middleware('access:گزارش لیست طلبکاران');
    Route::get('creditor/{counterparty}/show-add-tasvie', [CreditorReportController::class, 'showAddTasvie'])->name('creditor.showAddTasvie')->middleware('access:گزارش لیست طلبکاران');
    Route::post('creditor/add-tasvie', [CreditorReportController::class, 'addTasvie'])->name('creditor.addTasvie')->middleware('access:گزارش لیست طلبکاران');
    Route::get('creditor/{counterparty}/show-add-talab', [CreditorReportController::class, 'showAddTalab'])->name('creditor.showAddTalab')->middleware('access:گزارش لیست طلبکاران');
    Route::post('creditor/add-talab', [CreditorReportController::class, 'addTalab'])->name('creditor.addTalab')->middleware('access:گزارش لیست طلبکاران');
    Route::delete('creditor/delete/{id}', [CreditorReportController::class, 'delete'])->name('creditor.delete')->middleware('access:گزارش لیست طلبکاران');

    Route::get('financial-transactions/user', [FinancialTransactionController::class, 'userIndex'])->name('financial-transactions.user')->middleware('access:گزارش لیست طلبکاران');
    Route::get('financial-transactions/open-user-salary-advances/{counterparty}', [FinancialTransactionController::class, 'openUserSalaryAdvances'])->name('financial-transactions.openUserSalaryAdvances')->middleware('access:گزارش لیست طلبکاران');
    Route::get('financial-transactions/close-user-salary-advances/{counterparty}', [FinancialTransactionController::class, 'closeUserSalaryAdvances'])->name('financial-transactions.closeUserSalaryAdvances')->middleware('access:گزارش لیست طلبکاران');
    Route::get('financial-transactions/user/export', [FinancialTransactionController::class, 'userExport'])->name('financial-transactions.user.export')->middleware('access:گزارش لیست طلبکاران');
    Route::resource('financial-transactions', FinancialTransactionController::class)->middleware('access:گزارش لیست طلبکاران');
    Route::get('financial-transactions/{counterparty}/show-add-credit', [FinancialTransactionController::class, 'showAddCredit'])->name('financial-transactions.showAddCredit')->middleware('access:گزارش لیست طلبکاران');
    Route::post('financial-transactions/add-credit', [FinancialTransactionController::class, 'addCredit'])->name('financial-transactions.addCredit')->middleware('access:گزارش لیست طلبکاران');
    Route::get('financial-transactions/{counterparty}/show-add-debit/{onlyAssignedUsers?}', [FinancialTransactionController::class, 'showAddDebit'])->name('financial-transactions.showAddDebit')->middleware('access:گزارش لیست طلبکاران');
    Route::post('financial-transactions/add-debit', [FinancialTransactionController::class, 'addDebit'])->name('financial-transactions.addDebit')->middleware('access:گزارش لیست طلبکاران');

});

Route::get('workflow-report/daily-report/send-reminder', [DailyReportController:: class, 'sendReminder'])->middleware(['web'])->name('simpleWorkflowReport.daily-report.send-reminder');



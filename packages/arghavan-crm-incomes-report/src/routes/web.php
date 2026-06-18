<?php

use Arghavan\IncomesReport\Controllers\IndexIncomeReportController;
use Arghavan\IncomesReport\Controllers\IndexOutcomeReportController;
use Illuminate\Support\Facades\Route;

Route::name('incomes-report.')->prefix('incomes-report')->middleware(['web', 'auth','access:گزارش درآمدها'])->group(function(){
    Route::get('index', [IndexIncomeReportController::class, 'index'])->name('index');
});

Route::name('outcomes-report.')->prefix('outcomes-report')->middleware(['web', 'auth','access:گزارش هزینه‌ها'])->group(function(){
    Route::get('index', [IndexOutcomeReportController::class, 'index'])->name('index');
});

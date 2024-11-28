<?php

use Behin\PMCaseNumbering\Controllers\CaseNumberingController;
use Behin\PMCaseNumbering\Controllers\NewNummberController;
use Illuminate\Support\Facades\Route;

Route::name('pmCaseNo.')->prefix('pm-case-numbering')->middleware(['web'])->group(function(){
    Route::name('get.')->prefix('get')->group(function(){
        Route::get('new-number', [NewNummberController::class, 'getNewNumber'])->name('newNumber');
    });

    Route::name('form.')->prefix('form')->group(function(){
        Route::get('case-number', [CaseNumberingController::class, 'form'])->name('caseNumber');
    });
});
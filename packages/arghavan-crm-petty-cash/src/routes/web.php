<?php

use Arghavan\PettyCash\Controllers\DeletePettyCashController;
use Arghavan\PettyCash\Controllers\IndexPettyCashController;
use Arghavan\PettyCash\Controllers\StorePettyCashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('petty-cash.')->prefix('petty-cash')->middleware(['web', 'auth','access:تنخواه گردانی'])->group(function(){
    Route::get('index', [IndexPettyCashController::class, 'index'])->name('index');
    Route::post('store', [StorePettyCashController::class, 'store'])->name('store');
    Route::delete('{financialTransaction}/delete', [DeletePettyCashController::class, 'delete'])->name('delete');
});

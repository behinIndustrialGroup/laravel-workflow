<?php

use FileService\Controllers\FileServiceController;
use Illuminate\Support\Facades\Route;

Route::name('fileService.')->prefix('file-service')->middleware(['web', 'auth'])->group(function(){
    Route::get('get-file', [FileServiceController::class, 'uploadAndGetFile'])->name('uploadAndGetFile');
    });

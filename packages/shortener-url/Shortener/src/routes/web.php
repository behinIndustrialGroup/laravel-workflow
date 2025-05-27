<?php

use Illuminate\Support\Facades\Route;
use ShortenerUrl\Shortener\Http\Controllers\ShortLinkController;

Route::post('/shorten', [ShortLinkController::class, 'create']);
Route::get('/s/{code}', [ShortLinkController::class, 'redirect']);

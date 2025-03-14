<?php

use BehinInit\App\Http\Middleware\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Mkhodroo\AgencyInfo\Controllers\GetAgencyController;
use Pusher\Pusher;
use UserProfile\Controllers\ChangePasswordController;
use UserProfile\Controllers\GetUserAgenciesController;
use UserProfile\Controllers\NationalIdController;
use UserProfile\Controllers\UserProfileController;
use Illuminate\Support\Facades\Http;

Route::get('', function(){
    return view('auth.login');
});

require __DIR__.'/auth.php';

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', Access::class])->group(function(){
    Route::get('', function(){
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::any('/broadcasting/auth', function () {
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer 9924F201697A95F35835AF36734EB0BA50E9B99E7E5484EB00F1D3D52E51B90F',
    ])
    ->post('https://6eb4fa4d-2ab6-4d5e-bd9c-68f5668c732b.pushnotifications.pusher.com/publish_api/v1/instances/6eb4fa4d-2ab6-4d5e-bd9c-68f5668c732b/publishes', [
        'interests' => ['hello'],
        'web' => [
            'notification' => [
                'title' => 'Hello',
                'body' => 'Hello, world!',
            ],
        ],
    ]);
    
    // بررسی پاسخ دریافتی
    if ($response->successful()) {
        // درخواست موفقیت‌آمیز بوده است
        dd($response->json());
    } else {
        // درخواست با خطا مواجه شده است
        dd($response->body());
    }
})->middleware('auth');

Route::get('build-app', function(){
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('migrate');
    return redirect()->back();
});



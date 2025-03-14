<?php

use Behin\SimpleWorkflow\Controllers\Core\PushNotifications;
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

Route::get('/pusher/beams-auth', function (Request $request) {
    $userID = auth()->id(); // بررسی احراز هویت
    

    $beamsClient = new PushNotifications([
        'instanceId' => env('PUSHER_INSTANCE_ID'),
        'secretKey' => env('PUSHER_SECRET_KEY')
    ]);

    $beamsToken = $beamsClient->generateToken($userID);
    return response()->json($beamsToken);
})->middleware('auth');

Route::get('build-app', function(){
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('migrate');
    return redirect()->back();
});



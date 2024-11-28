<?php

use BehinInit\App\Http\Middleware\Access;
use Illuminate\Support\Facades\Route;
use BehinUserRoles\Controllers\GetMethodsController;
use BehinUserRoles\Controllers\GetRoleController;
use BehinUserRoles\Controllers\UserController;

Route::name('role.')->prefix('role')->middleware(['web', 'auth',Access::class])->group(function(){
    Route::get('list-form', [GetRoleController::class, 'listForm'])->name('listForm');
    Route::get('list', [GetRoleController::class, 'list'])->name('list');
    Route::post('get', [GetRoleController::class, 'get'])->name('get');
    Route::post('edit', [GetRoleController::class, 'edit'])->name('edit');
    Route::post('change-user-role', [GetRoleController::class, 'changeUserRole'])->name('changeUserRole');
});

Route::name('method.')->prefix('method')->middleware(['web', 'auth',Access::class])->group(function(){
    Route::get('list', [GetMethodsController::class, 'list'])->name('list');
    Route::post('edit', [GetMethodsController::class, 'edit'])->name('edit');
});

Route::prefix('/user')->middleware(['web', 'auth',Access::class])->group(function () {
    Route::get('/{id}', [UserController::class, 'index'])->name('user.all');
    Route::post('/{id}', [UserController::class, 'AccessReg']);

    Route::post('/{id}/changepass', [UserController::class, 'ChangePass']);
    Route::post('/{id}/change-pm-username', [UserController::class, 'changePMUsername'])->name('change-pm-username');
    Route::post('/{id}/change-ip', [UserController::class, 'ChangeIp'])->name('change-user-ip');

    Route::post('/{id}/changeShowInReport', [UserController::class, 'changeShowInReport']);
});

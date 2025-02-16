<?php

use BehinInit\App\Http\Middleware\Access;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;
use BehinUserRoles\Controllers\GetMethodsController;
use BehinUserRoles\Controllers\GetRoleController;
use BehinUserRoles\Controllers\UserController;

Route::name('role.')->prefix('role')->middleware(['web', 'auth'])->group(function(){
    Route::get('list-form', [GetRoleController::class, 'listForm'])->name('listForm');
    Route::get('list', [GetRoleController::class, 'list'])->name('list');
    Route::get('show/{id}', [GetRoleController::class, 'show'])->name('show');
    Route::get('copy/{id}', [GetRoleController::class, 'copy'])->name('copy');
    Route::post('get', [GetRoleController::class, 'get'])->name('get');
    Route::post('edit', [GetRoleController::class, 'edit'])->name('edit');
    Route::put('change-user-role', [GetRoleController::class, 'changeUserRole'])->name('changeUserRole');
});

Route::name('method.')->prefix('method')->middleware(['web', 'auth',Access::class])->group(function(){
    Route::get('list', [GetMethodsController::class, 'list'])->name('list');
    Route::post('edit', [GetMethodsController::class, 'edit'])->name('edit');
});

Route::prefix('/user')->middleware(['web', 'auth',Access::class])->group(function () {
    Route::get('/{id}', [UserController::class, 'index'])->name('user.all');
    Route::post('/{id}', [UserController::class, 'AccessReg']);
    Route::put('/{id}/update', [UserController::class, 'update'])->name('user.update');

    Route::post('/{id}/changepass', [UserController::class, 'ChangePass'])->name('user.ChangePass');
    Route::post('/{id}/change-pm-username', [UserController::class, 'changePMUsername'])->name('change-pm-username');
    Route::post('/{id}/change-ip', [UserController::class, 'ChangeIp'])->name('change-user-ip');

    Route::post('/{id}/changeShowInReport', [UserController::class, 'changeShowInReport']);
    Route::post('/{id}/addToDepartment', [UserController::class, 'addToDepartment'])->name('user.addToDepartment');
    Route::delete('/{id}/removeFromDepartment', [UserController::class, 'removeFromDepartment'])->name('user.removeFromDepartment');
});

Route::resource('users', UserController::class)->middleware(['web', 'auth',Access::class]);

Route::resource('department', DepartmentController::class)->middleware(['web', 'auth',Access::class]);

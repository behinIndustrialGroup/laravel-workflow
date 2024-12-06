<?php

use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FieldController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\RoutingController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskActorController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Support\Facades\Route;

Route::name('simpleWorkflow.')->prefix('workflow')->middleware(['web', 'auth'])->group(function(){
    Route::name('process.')->prefix('process')->group(function(){
        Route::get('', [ ProcessController::class, 'index' ])->name('index');
        Route::get('create', [ ProcessController::class, 'create' ])->name('create');
        Route::post('store', [ ProcessController::class, 'store' ])->name('store');
        Route::get('start-list', [ ProcessController::class, 'startListView' ])->name('startListView');
        Route::get('start/{taskId}', [ ProcessController::class, 'start' ])->name('start');

    });

    Route::name('task.')->prefix('task')->group(function(){
        Route::get('index/{process_id}', [ TaskController::class, 'index' ])->name('index');
        Route::post('create', [ TaskController::class, 'create' ])->name('create');
        Route::get('{task}/edit', [ TaskController::class, 'edit' ])->name('edit');
        Route::put('{task}/update', [ TaskController::class, 'update' ])->name('update');

        Route::get('actor/{taskId}', [ TaskController::class, 'index' ])->name('actor');

    });

    Route::name('form.')->prefix('form')->group(function(){
        Route::get('index', [ FormController::class, 'index' ])->name('index');
        Route::get('edit/{id}', [ FormController::class, 'edit' ])->name('edit');
        Route::post('update', [ FormController::class, 'update' ])->name('update');
        Route::post('store', [ FormController::class, 'store' ])->name('store');
        Route::post('create', [ FormController::class, 'createForm' ])->name('create');
    });

    Route::resource('scripts', ScriptController::class);
    Route::resource('conditions', ConditionController::class);
    Route::resource('task-actors', TaskActorController::class);
    Route::resource('fields', FieldController::class);

    Route::name('inbox.')->prefix('inbox')->group(function(){
        Route::get('', [ InboxController::class, 'index' ])->name('index');
        Route::get('view/{inboxId}', [ InboxController::class, 'view' ])->name('view');
    });

    Route::name('routing.')->prefix('routing')->group(function(){
        Route::post('save', [ RoutingController::class, 'save' ])->name('save');
        Route::post('save-and-next', [ RoutingController::class, 'saveAndNext' ])->name('saveAndNext');
        Route::get('view/{inboxId}', [ InboxController::class, 'view' ])->name('view');
    });
});

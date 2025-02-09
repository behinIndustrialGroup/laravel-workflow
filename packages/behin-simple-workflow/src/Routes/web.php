<?php

use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\DoneInboxController;
use Behin\SimpleWorkflow\Controllers\Core\EntityController;
use Behin\SimpleWorkflow\Controllers\Core\FieldController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\RoutingController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskActorController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\TaskJumpController;
use Illuminate\Support\Facades\Route;

Route::name('simpleWorkflow.')->prefix('workflow')->middleware(['web', 'auth'])->group(function(){
    Route::name('process.')->prefix('process')->group(function(){
        Route::get('', [ ProcessController::class, 'index' ])->name('index');
        Route::get('create', [ ProcessController::class, 'create' ])->name('create');
        Route::post('store', [ ProcessController::class, 'store' ])->name('store');
        Route::get('start-list', [ ProcessController::class, 'startListView' ])->name('startListView');
        Route::get('start/{taskId}/{force?}/{redirect?}/{inDraft}', [ ProcessController::class, 'start' ])->name('start');
        Route::get('check-error/{processId}', [ ProcessController::class, 'processHasError' ])->name('processHasError');
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
        Route::get('edit-content/{id}', [ FormController::class, 'editContent' ])->name('editContent');
        Route::post('updateContent', [ FormController::class, 'updateContent' ])->name('updateContent');
        Route::get('edit-script/{id}', [ FormController::class, 'editScript' ])->name('editScript');
        Route::post('updateScript', [ FormController::class, 'updateScript' ])->name('updateScript');
        Route::post('store', [ FormController::class, 'store' ])->name('store');
        Route::post('create', [ FormController::class, 'createForm' ])->name('create');
        Route::post('copy', [ FormController::class, 'copy' ])->name('copy');
        Route::post('delete', [ FormController::class, 'delete' ])->name('delete');
    });

    Route::resource('scripts', ScriptController::class);
    Route::post('scripts/{id}/test', [ ScriptController::class, 'test' ])->name('scripts.test');
    Route::any('scripts/{id}/run', [ ScriptController::class, 'runFromView' ])->name('scripts.run');

    Route::resource('conditions', ConditionController::class);
    Route::resource('task-actors', TaskActorController::class);
    Route::resource('fields', FieldController::class);

    Route::name('inbox.')->prefix('inbox')->group(function(){
        Route::get('', [ InboxController::class, 'index' ])->name('index');
        Route::get('done-inbox', [ DoneInboxController::class, 'index' ])->name('done');
        // Route::get('all-inbox', [ InboxController::class, 'getAllInbox' ])->name('getAllInbox');
        Route::get('cases', [ InboxController::class, 'showCases' ])->name('cases.list');
        Route::get('cases/{caseId}/inboxes', [ InboxController::class, 'showInboxes' ])->name('cases.inboxes');
        Route::get('view/{inboxId}', [ InboxController::class, 'view' ])->name('view');
        Route::get('edit/{inboxId}', [ InboxController::class, 'edit' ])->name('edit');
        Route::put('update/{inboxId}', [ InboxController::class, 'update' ])->name('update');
        Route::get('delete/{inboxId}', [ InboxController::class, 'delete' ])->name('delete');
    });

    Route::name('routing.')->prefix('routing')->group(function(){
        Route::post('create-case-number-and-save', [ RoutingController::class, 'createCaseNumberAndSave' ])->name('createCaseNumberAndSave');
        Route::post('save', [ RoutingController::class, 'save' ])->name('save');
        Route::post('save-and-next', [ RoutingController::class, 'saveAndNext' ])->name('saveAndNext');
        Route::post('jump-to', [ RoutingController::class, 'jumpTo' ])->name('jumpTo');
        Route::get('view/{inboxId}', [ InboxController::class, 'view' ])->name('view');
    });


    Route::resource('entities', EntityController::class);
    Route::get('entities/{entity}/create-table', [EntityController::class, 'createTable'])->name('entities.createTable');

    Route::resource('task-jump', TaskJumpController::class);
    Route::get('task-jump/{task_id}/{inbox_id}/{case_id}/{process_id}', [TaskJumpController::class, 'show'])->name('task-jump.show');

});

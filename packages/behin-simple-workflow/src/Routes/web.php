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
use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
use Illuminate\Support\Facades\Route;

Route::name('simpleWorkflow.')->prefix('workflow')->middleware(['web', 'auth'])->group(function(){
    Route::name('process.')->prefix('process')->group(function(){
        Route::get('', [ ProcessController::class, 'index' ])->name('index');
        Route::get('create', [ ProcessController::class, 'create' ])->name('create');
        Route::post('store', [ ProcessController::class, 'store' ])->name('store');
        Route::get('{processId}/edit', [ ProcessController::class, 'edit' ])->name('edit');
        Route::put('{processId}', [ ProcessController::class, 'update' ])->name('update');
        Route::get('start-list', [ ProcessController::class, 'startListView' ])->name('startListView');

        Route::get('check-error/{processId}', [ ProcessController::class, 'processHasError' ])->name('processHasError');
        Route::get('{processId}/export-view', [ ProcessController::class, 'exportView' ])->name('exportView');
        Route::get('import-view', [ ProcessController::class, 'importView' ])->name('importView');
        Route::get('{processId}/export', [ ProcessController::class, 'export' ])->name('export');
        Route::post('import', [ ProcessController::class, 'import' ])->name('import');
    });

    Route::name('task.')->prefix('task')->group(function(){
        Route::get('index/{process_id}', [ TaskController::class, 'index' ])->name('index');
        Route::post('create', [ TaskController::class, 'create' ])->name('create');
        Route::get('{task}/edit', [ TaskController::class, 'edit' ])->name('edit');
        Route::put('{task}/update', [ TaskController::class, 'update' ])->name('update');
        Route::delete('{task}/delete', [ TaskController::class, 'destroy' ])->name('delete');

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
        Route::post('open/{form_id}', [ FormController::class, 'open' ])->name('open');
        Route::post('open-create-new/{form_id}', [ FormController::class, 'openCreateNew' ])->name('open');
    });

    Route::post('scripts/export', [ScriptController::class, 'export'])->name('scripts.export');
    Route::post('scripts/import', [ScriptController::class, 'import'])->name('scripts.import');
    Route::post('scripts/{script}/copy', [ScriptController::class, 'copy'])->name('scripts.copy');
    Route::resource('scripts', ScriptController::class);
    Route::post('scripts/{id}/test', [ ScriptController::class, 'test' ])->name('scripts.test');
    Route::any('scripts/{id}/run', [ ScriptController::class, 'runFromView' ])->name('scripts.run');
    Route::post('/scripts/autocomplete', [ScriptController::class, 'autocomplete'])->name('scripts.autocomplete');


    Route::resource('conditions', ConditionController::class);
    Route::post('conditions/{id}/test', [ ConditionController::class, 'runConditionForTest' ])->name('conditions.test');
    Route::resource('task-actors', TaskActorController::class);
    Route::post('fields/export', [FieldController::class, 'export'])->name('fields.export');
    Route::post('fields/import', [FieldController::class, 'import'])->name('fields.import');
    Route::resource('fields', FieldController::class);
    Route::get('fields/{field}/copy', [FieldController::class, 'copy'])->name('fields.copy');

    Route::name('inbox.')->prefix('inbox')->group(function(){
        Route::get('categorized', [ InboxController::class, 'categorized' ])->name('categorized');
        Route::get('', [ InboxController::class, 'index' ])->name('index');
        Route::get('done-inbox', [ DoneInboxController::class, 'index' ])->name('done');
        // Route::get('all-inbox', [ InboxController::class, 'getAllInbox' ])->name('getAllInbox');
        Route::get('cases', [ InboxController::class, 'showCases' ])->name('cases.list');
        Route::get('cases/{caseId}/inboxes', [ InboxController::class, 'showInboxes' ])->name('cases.inboxes');
        Route::post('{inbox}/reminders', [ InboxController::class, 'storeReminder' ])->name('reminders.store');

        Route::get('edit/{inboxId}', [ InboxController::class, 'edit' ])->name('edit');
        Route::put('update/{inboxId}', [ InboxController::class, 'update' ])->name('update');
        Route::get('change-status/{inboxId}', [ InboxController::class, 'changeStatus' ])->name('changeStatus');
        Route::get('cancel/{inboxId}', [ InboxController::class, 'cancel' ])->name('cancel');
        Route::get('delete/{inboxId}', [ InboxController::class, 'delete' ])->name('delete');
        Route::get('case-history/{caseNumber?}', [InboxController::class, 'caseHistory'])->name('caseHistoryView');
    });

    Route::name('routing.')->prefix('routing')->group(function(){
        Route::post('create-case-number-and-save', [ RoutingController::class, 'createCaseNumberAndSave' ])->name('createCaseNumberAndSave');
        Route::post('save', [ RoutingController::class, 'save' ])->name('save');
        Route::post('save-and-next', [ RoutingController::class, 'saveAndNext' ])->name('saveAndNext');
        Route::post('jump-back', [ RoutingController::class, 'jumpBack' ])->name('jumpBack');
        Route::post('jump-to', [ RoutingController::class, 'jumpTo' ])->name('jumpTo');
        Route::get('view/{inboxId}', [ InboxController::class, 'view' ])->name('view');
    });

    Route::resource('entities', EntityController::class);
    Route::post('entities/export', [EntityController::class, 'export'])->name('entities.export');
    Route::post('entities/import', [EntityController::class, 'import'])->name('entities.import');
    Route::get('entities/{entity}/create-table', [EntityController::class, 'createTable'])->name('entities.createTable');
    Route::get('entities/{entity}/records', [EntityController::class, 'records'])->name('entities.records');
    Route::get('entities/{entity}/records/create', [EntityController::class, 'createRecord'])->name('entities.createRecord');
    Route::post('entities/{entity}/records', [EntityController::class, 'storeRecord'])->name('entities.storeRecord');
    Route::post('entities/{entity}/records/export', [EntityController::class, 'exportRecords'])->name('entities.records.export');
    Route::post('entities/{entity}/records/import', [EntityController::class, 'importRecords'])->name('entities.records.import');
    Route::get('entities/{entity}/records/{id}/edit', [EntityController::class, 'editRecord'])->name('entities.editRecord');
    Route::put('entities/{entity}/records/{id}', [EntityController::class, 'updateRecord'])->name('entities.updateRecord');
    Route::delete('entities/{entity}/records/{id}', [EntityController::class, 'deleteRecord'])->name('entities.deleteRecord');

    Route::resource('task-jump', TaskJumpController::class);
    Route::get('task-jump/{task_id}/{inbox_id}/{case_id}/{process_id}', [TaskJumpController::class, 'show'])->name('task-jump.show');

    Route::resource('view-model', ViewModelController::class);
    Route::get('view-model/{view_model}/copy', [ViewModelController::class, 'copy'])->name('view-model.copy');
    Route::post('view-model/export', [ViewModelController::class, 'export'])->name('view-model.export');
    Route::post('view-model/import', [ViewModelController::class, 'import'])->name('view-model.import');
    Route::post('get-view-model-rows', [ViewModelController::class, 'getRows'])->name('view-model.get-rows');
    Route::post('update-view-model-record', [ViewModelController::class, 'updateRecord'])->name('view-model.update-record');
    Route::post('delete-view-model-record', [ViewModelController::class, 'deleteRecord'])->name('view-model.delete-record');
});

Route::get('workflow/process/start/{taskId}/{force?}/{redirect?}/{inDraft?}', [ ProcessController::class, 'start' ])->name('simpleWorkflow.process.start')->middleware('web');
Route::get('workflow/inbox/view/{inboxId}', [ InboxController::class, 'view' ])->name('simpleWorkflow.inbox.view')->middleware(['web']);


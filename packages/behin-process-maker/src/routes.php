<?php

namespace BehinProcessMaker;

use App\CustomClasses\SimpleXLSX;
use BehinInit\App\Http\Middleware\Access;
use Exception;
use Illuminate\Support\Facades\Route;
use BehinProcessMaker\Controllers\AuthController;
use BehinProcessMaker\Controllers\CaseController;
use BehinProcessMaker\Controllers\CaseTrackerController;
use BehinProcessMaker\Controllers\ClaimCaseController;
use BehinProcessMaker\Controllers\CurlRequestController;
use BehinProcessMaker\Controllers\DeleteCaseController;
use BehinProcessMaker\Controllers\DeleteVarController;
use BehinProcessMaker\Controllers\DoneCaseController;
use BehinProcessMaker\Controllers\DraftCaseController;
use BehinProcessMaker\Controllers\DynaFormController;
use BehinProcessMaker\Controllers\GetCaseVarsController;
use BehinProcessMaker\Controllers\InboxController;
use BehinProcessMaker\Controllers\InputDocController;
use BehinProcessMaker\Controllers\NewCaseController;
use BehinProcessMaker\Controllers\PMVacationController;
use BehinProcessMaker\Controllers\ProcessController;
use BehinProcessMaker\Controllers\ProcessMapController;
use BehinProcessMaker\Controllers\SetCaseVarsController;
use BehinProcessMaker\Controllers\StartCaseController;
use BehinProcessMaker\Controllers\StepController;
use BehinProcessMaker\Controllers\TaskController;
use BehinProcessMaker\Controllers\ToDoCaseController;
use BehinProcessMaker\Controllers\TriggerController;
use BehinProcessMaker\Controllers\User\CreateUserController;
use BehinProcessMaker\Controllers\VariableController;
use Illuminate\Http\Request;
use Mkhodroo\PMReport\Controllers\TableController;

Route::name('MkhodrooProcessMaker.')->prefix('pm')->middleware(['web', 'auth', Access::class])->group(function () {
    Route::get('inbox', [CaseController::class, 'get'])->name('inbox');
    Route::get('new-case', [CaseController::class, 'newCase'])->name('newCase');
    Route::name('report.')->prefix('report')->group(function () {
        Route::get('number-of-my-vacations', [PMVacationController::class, 'numberOfMyVacation'])->name('numberOfMyVacation');
    });
    Route::name('forms.')->prefix('forms')->group(function () {
        Route::get('start', [StartCaseController::class, 'form'])->name('start');
        Route::get('todo', [ToDoCaseController::class, 'form'])->name('todo');
        Route::get('draft', [DraftCaseController::class, 'form'])->name('draft');
        Route::get('done', [DoneCaseController::class, 'form'])->name('done');
    });

    Route::name('api.')->prefix('api')->group(function () {
        Route::get('start-process', [StartCaseController::class, 'get'])->name('startProcess');
        Route::post('new-case', [NewCaseController::class, 'create'])->name('newCase');
        Route::get('todo', [ToDoCaseController::class, 'getMyCase'])->name('todo');
        Route::get('draft', [DraftCaseController::class, 'getMyCase'])->name('draft');
        Route::get('done', [DoneCaseController::class, 'getMyCase'])->name('done');
        Route::post('get-case-dynaForm', [DynaFormController::class, 'get'])->name('getCaseDynaForm');
        Route::post('get-case-mainForm', [DoneCaseController::class, 'mainForm'])->name('getCaseMainForm');
        Route::post('save-and-next', [SetCaseVarsController::class, 'saveAndNext'])->name('saveAndNext');
        Route::post('save', [SetCaseVarsController::class, 'save'])->name('save');
        Route::get('get-case-vars/{caseId}', [GetCaseVarsController::class, 'getByCaseId'])->name('getCaseVars');
        Route::get('get-case-info/{caseId}/{delIndex}', [CaseController::class, 'getCaseInfo'])->name('getCaseInfo');
        Route::get('get-case-main-info/{caseId}', [GetCaseVarsController::class, 'getMainInfoByCaseId'])->name('getMainInfoByCaseId');
        Route::get('get-case-process-map/{caseId}', [ProcessMapController::class, 'getCaseProcessMap'])->name('getCaseProcessMap');
        Route::get('delete-case/{caseId}', [DeleteCaseController::class, 'byCaseId'])->name('deleteCase');
        Route::get('get-trigger-list', [TriggerController::class, 'list'])->name('getTriggerList');
        Route::get('get-task/{taskId}', [TaskController::class, 'getByTaskId'])->name('getTask');
        Route::get('get-tasks-by-process/{processId}', [TaskController::class, 'getByProcessId'])->name('getTaskByProcessId');
        Route::post('delete-doc', [DeleteVarController::class, 'deleteDoc'])->name('deleteDoc');

        Route::name('process.')->prefix('process')->group(function () {
            Route::get('get-by-id/{process_id}', [ProcessController::class, 'getNameById']);
        });
        Route::name('user.')->prefix('user')->group(function () {
            Route::post('create', [CreateUserController::class, 'create'])->name('create');
        });
    });

    Route::get('test', function(){
        ClaimCaseController::claim('36482776566abcb93cbd923027187390');
    });
});

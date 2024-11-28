<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use TodoList\Controllers\OthersTodoListController;
use TodoList\Controllers\TodoListController;

Route::name('todoList.')->prefix('todo-list')->middleware(['web', 'auth'])->group(function(){
    Route::get('index', [TodoListController::class, 'index'])->name('index');
    Route::get('list', [TodoListController::class, 'list'])->name('list');
    Route::post('create', [TodoListController::class, 'create'])->name('create');
    Route::post('edit', [TodoListController::class, 'edit'])->name('edit');
    Route::put('update', [TodoListController::class, 'update'])->name('update');
    Route::delete('delete', [TodoListController::class, 'delete'])->name('delete');
    Route::get('today', [TodoListController::class, 'today'])->name('today');
    Route::get('expired', [TodoListController::class, 'expired'])->name('expired');
    Route::get('others-list', [TodoListController::class, 'othersList'])->name('othersList');
    });

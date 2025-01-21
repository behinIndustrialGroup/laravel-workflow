<?php

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FieldController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Morilog\Jalali\Jalalian;

if (!function_exists('getProcesses')) {
    function getProcesses() {
        return ProcessController::getAll();
    }
}

if (!function_exists('getCases')) {
    function getCases() {
        return CaseController::getAll();
    }
}

if (!function_exists('getProcessForms')) {
    function getProcessForms() {
        return FormController::getAll();
    }
}


if (!function_exists('getProcessScripts')) {
    function getProcessScripts() {
        return ScriptController::getAll();
    }
}

if (!function_exists('getProcessConditions')) {
    function getProcessConditions() {
        return ConditionController::getAll();
    }
}

if (!function_exists('getProcessTasks')) {
    function getProcessTasks() {
        return TaskController::getAll();
    }
}

if (!function_exists('getProcessFields')) {
    function getProcessFields() {
        return FieldController::getAll();
    }
}

if (!function_exists('getFieldDetailsByName')) {
    function getFieldDetailsByName($fieldName) {
        return FieldController::getByName($fieldName);
    }
}

if (!function_exists('previewForm')) {
    function previewForm($id) {
        return FormController::preview($id);
    }
}

if (!function_exists('taskHasError')) {
    function taskHasError($taskId) {
        return TaskController::TaskHasError($taskId);
    }
}

if (!function_exists('getUserInfo')) {
    function getUserInfo($userId) {
        $user = User::find($userId);
        if($user){
            return $user;
        }
        return User::where('pm_user_uid', $userId)->first();
    }
}

if (!function_exists('runScript')) {
    function runScript($id, $caseId) {
        return ScriptController::runScript($id, $caseId);
    }
}

if(!function_exists('toJalali')){
    function toJalali($date){
        $jDate = Jalalian::fromCarbon($date);
        return $jDate;
    }
}

if(!function_exists('getFormInformation')){
    function getFormInformation($id){
        return FormController::getById($id);
    }
}





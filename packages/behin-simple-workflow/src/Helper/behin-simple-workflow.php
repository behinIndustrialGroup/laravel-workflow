<?php

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FieldController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

if (!function_exists('getProcesses')) {
    function getProcesses() {
        // Log::info("function getProcess Used By user". Auth::user()->name);
        return ProcessController::getAll();
    }
}

if (!function_exists('getCases')) {
    function getCases() {
        // Log::info("function getCases Used By user". Auth::user()->name);
        return CaseController::getAll();
    }
}

if (!function_exists('getProcessForms')) {
    function getProcessForms() {
        // Log::info("function getProcessForms Used By user". Auth::user()->name);
        return FormController::getAll();
    }
}


if (!function_exists('getProcessScripts')) {
    function getProcessScripts() {
        // Log::info("function getProcessScripts Used By user". Auth::user()->name);
        return ScriptController::getAll();
    }
}

if (!function_exists('getProcessConditions')) {
    function getProcessConditions() {
        // Log::info("function getProcessConditions Used By user". Auth::user()->name);
        return ConditionController::getAll();
    }
}

if (!function_exists('getProcessTasks')) {
    function getProcessTasks() {
        // Log::info("function getProcessTasks Used By user". Auth::user()->name);
        return TaskController::getAll();
    }
}

if (!function_exists('getProcessFields')) {
    function getProcessFields() {
        // Log::info("function getProcessFields Used By user". Auth::user()->name);
        return FieldController::getAll();
    }
}

if (!function_exists('getFieldDetailsByName')) {
    function getFieldDetailsByName($fieldName) {
        // Log::info("function getFieldDetailsByName Used By user". Auth::user()->name);
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
        // Log::info("function taskHasError Used By user". Auth::user()->name);
        return TaskController::TaskHasError($taskId);
    }
}

if (!function_exists('getUserInfo')) {
    function getUserInfo($userId) {
        // Log::info("function getUserInfo Used By user". Auth::user()->name);
        if(!$userId){
            return null;
        }
        $user = User::find($userId);
        if($user){
            return $user;
        }
        return User::where('pm_user_uid', $userId)->first();
    }
}

if (!function_exists('runScript')) {
    function runScript($id, $caseId) {
        // Log::info("function runScript Used By user". Auth::user()->name);
        return ScriptController::runScript($id, $caseId);
    }
}

if(!function_exists('toJalali')){
    function toJalali($date){
        if(gettype($date) == 'string'){
            $date = Carbon::parse($date);
        }
        if(gettype($date) == 'integer'){
            $date = Carbon::createFromTimestamp($date, 'Asia/Tehran');
        }
        // Log::info("function toJalali Used By user". Auth::user()->name);
        $jDate = Jalalian::fromCarbon($date);
        return $jDate;
    }
}

if(!function_exists('getFormInformation')){
    function getFormInformation($id){
        // Log::info("function getFormInformation Used By user". Auth::user()->name);
        return FormController::getById($id);
    }
}

if (!function_exists('convertPersianToEnglish')) {
    function convertPersianToEnglish($string) {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($persianNumbers, $englishNumbers, $string);
    }
}



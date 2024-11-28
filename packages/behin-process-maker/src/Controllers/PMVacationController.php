<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use BehinProcessMaker\Models\PMVacation;

class PMVacationController extends Controller
{
    
    function numberOfMyVacation() {
        $user_id = PMUserController::getByName(auth()->user()->pm_username)->USR_UID;
        return [
            'daily' => PMVacation::where('USER_ID', $user_id)->where('TYPE', 'daily')->where('DEPARTMENTMANAGERRESULT', 1)->sum('DURATION'),
            'hourly' => PMVacation::where('USER_ID', $user_id)->where('TYPE', 'hourly')->where('DEPARTMENTMANAGERRESULT', 1)->sum('DURATION'),
        ];
    }
}

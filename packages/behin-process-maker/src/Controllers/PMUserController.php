<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use BehinProcessMaker\Models\PMUsers;
use BehinProcessMaker\Models\PMVacation;

class PMUserController extends Controller
{
    public static function getByName($user_name) {
        return PMUsers::where('USR_USERNAME', $user_name)->first();
    }

    public static function getUserByPmUserId($pm_user_uid){
        return User::where('pm_user_uid', $pm_user_uid)->first();
    }
}

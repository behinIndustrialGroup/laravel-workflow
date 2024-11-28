<?php 

namespace BehinProcessMaker\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use BehinProcessMaker\Controllers\AuthController;
use BehinProcessMaker\Controllers\CurlRequestController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GetUserController extends Controller
{
    public static function getUserLocalInfoByPmUserId($pm_user_uid){
        return User::where('pm_user_uid', $pm_user_uid)->first();
    }
}


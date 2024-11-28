<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Behin\Sms\Controllers\SmsController;
use BehinProcessMaker\Controllers\User\GetUserController;
use BehinUserRoles\Controllers\UserController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mkhodroo\PMReport\Controllers\TableController;

class SendSmsController extends Controller
{

    public static function toNextUser($app_uid, $del_index)
    {        
        $info = CaseController::getCaseInfo($app_uid, $del_index);
        try{
            $user_uid = $info?->currentUsers?->userId;
            if($user_uid){
                $user = GetUserController::getUserLocalInfoByPmUserId($user_uid);
                SmsController::send($user->email, config('pm_config.send_sms_to_next_user_text'));
            }
        }catch(Exception $e){
            
        }
        
    }
}
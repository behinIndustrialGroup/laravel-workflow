<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;

class AuthController extends Controller
{
    private static $pmServer;
    private static $pmWorkspace;

    public static function getAccessToken()
    {
        $user = self::getAuthUser();
        $now = Carbon::now();
        $diff = $now->diffInMinutes($user->pm_user_access_token_exp_date);
        // Log::info($diff);
        if($diff > 0 and $diff < config('pm_config.access_token_exp_in_minute') and $user->pm_user_access_token){
            return $user->pm_user_access_token;
        }
        // Log::info("Get Access Token Api Called");
        self::$pmServer = str_replace('https', 'http', env('PM_SERVER'));
        self::$pmWorkspace = "workflow";
        $postParams = array(
            'grant_type'    => 'password',
            'scope'         => '*',       //set to 'view_process' if not changing the process
            'client_id'     => env('PM_CLIENT_ID'),
            'client_secret' => env('PM_CLIENT_SECRET'),
            'username'      => $user->pm_username,
            'password'      => $user->pm_user_password
        );
        $ch = curl_init(self::$pmServer . '/' . self::$pmWorkspace . "/oauth2/token");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $oToken = json_decode(curl_exec($ch));
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpStatus != 200) {
            Log::info("Error in HTTP status code: $httpStatus\n");
            return false;
        } elseif (isset($oToken->error)) {
            Log::info("Error logging into " . self::$pmServer . ":\n" .
                "Error:       {$oToken->error}\n" .
                "Description: {$oToken->error_description}\n");
            return false;
        } else {
            // Log::info(var_dump($oToken));
            $user->pm_user_access_token = $oToken->access_token;
            $user->pm_user_access_token_exp_date = Carbon::now()->addMinutes(config('pm_config.access_token_exp_in_minute'));
            if(!$user->pm_user_uid){
                $UID = RestApiController::getUserId();
                $user->pm_user_uid = $UID;
            }
            $user->save();
            return $oToken->access_token;
        }

        return $oToken;
    }

    public static function wsdl_login()
    {
        $user = self::getAuthUser();
        $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')). '/sysworkflow/en/green/services/wsdl2');
        $params = array(array('userid' => $user->pm_username, 'password' => $user->pm_user_password));
        $result = $client->__SoapCall('login', $params);
        return $result;
    }

    private static function getAuthUser(){
        $user = Auth::user();
        if(!$user->pm_user_password){
            $newPass = RestApiController::changePass();
            $user = User::find($user->id);
            $user->pm_user_password = $newPass;
            $user->save();
        }
        return $user;
    }
}

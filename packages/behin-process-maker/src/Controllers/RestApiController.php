<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;
use Illuminate\Support\Str;

class RestApiController extends Controller
{
    private static $pmServer;
    private static $pmWorkspace;

    public static function login() {
        $client = new SoapClient(env('PM_SERVER').'sysworkflow/en/neoclassic/services/wsdl2');
        $params = array(array('userid'=>'f.shahidi', 'password'=>'Fsh123456'));
        $result = $client->__SoapCall('login', $params);
        return $result;
    }

    public static function getAccessToken(){
        self::$pmServer = str_replace('https', 'http', env('PM_SERVER')) ;
        self::$pmWorkspace = "workflow";
        $postParams = array(
            'grant_type'    => 'password',
            'scope'         => '*',       //set to 'view_process' if not changing the process
            'client_id'     => env('PM_CLIENT_ID'),
            'client_secret' => env('PM_CLIENT_SECRET'),
            'username'      => env('PM_ADMIN_USER'),
            'password'      => env('PM_ADMIN_PASS')
        );
        $ch = curl_init(self::$pmServer . '/'. self::$pmWorkspace . "/oauth2/token");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $oToken = json_decode(curl_exec($ch));
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpStatus != 200) {
            Log::info("Error in HTTP status code: $httpStatus\n");
            return false;
        }
        elseif (isset($oToken->error)) {
            Log::info("Error logging into " . self::$pmServer .":\n" .
                "Error:       {$oToken->error}\n" .
                "Description: {$oToken->error_description}\n");
                return false;
        }
        else {
            // Log::info($oToken->access_token);
            return $oToken->access_token;
        }
        
        return $oToken;
    }

    public static function changePass($accessToken = null, $userId = null, $newPass = null){
        $accessToken = self::getAccessToken();
        $userId = self::getUserId($accessToken);
        $newPass = Str::random(10);
        $postParams = array(
            'usr_new_pass'      => $newPass,
            'usr_cnf_pass' => $newPass
          );
          
          $ch = curl_init(self::$pmServer . "/api/1.0/workflow/user/" . $userId);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken"));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

          $oResult = json_decode(curl_exec($ch));
          $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

          
          if (isset($oResult) and isset($oResult->error)) {
             print "Error in ".self::$pmServer.": \nCode: {$oResult->error->code}\nMessage: {$oResult->error->message}\n";
             return null;
          }
          elseif ($statusCode != 200) {
            Log::info("Error updating user: HTTP status code: $statusCode\n");
            return null;
          }
          else {
             return $newPass;
          }
    }

    public static function getUserId($accessToken = null){
        Log::info(__CLASS__);
        $accessToken = self::getAccessToken();
        $username = Auth::user()->pm_username;
        if(!$username){
            return false;
        }
        $ch = curl_init(self::$pmServer . "/api/1.0/workflow/users?filter=$username");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $aUsers = json_decode(curl_exec($ch));
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $aActiveUsers = array();

        if ($statusCode != 200) {
            if (isset ($aUsers) and isset($aUsers->error))
                Log::info("Error code: {$aUsers->error->code}\nMessage: {$aUsers->error->message}\n");
            else
                Log::info("Error: HTTP status code: $statusCode\n");
            return false;
        }
        else {
            return count($aUsers) ? $aUsers[0]->usr_uid : false;;
            foreach ($aUsers as $oUser) {
                if ($oUser->usr_status == "ACTIVE") {
                    $aActiveUsers[] = array("uid" => $oUser->usr_uid, "username" => $oUser->usr_username);
                }
            }
        }
    }

    public static function createUser($accessToken, $user){
        if(!$user->pm_username){
            return false;
        }
        $pass = rand(10000000, 99999999);
        $postParams = array(
            'usr_username'   => $user->pm_username,
            'usr_firstname'  => $user->name,
            'usr_lastname'   => ".",
            'usr_email'      => "$user->email@altfuel.ir",
            'usr_due_date'   => "2030-12-31",
            'usr_status'     => "ACTIVE",
            'usr_role'       => "PROCESSMAKER_OPERATOR",
            'usr_new_pass'   => "$pass",
            'usr_cnf_pass'   => "$pass",
         );
         
         $ch = curl_init(self::$pmServer . "/api/1.0/workflow/user");
         curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken"));
         curl_setopt($ch, CURLOPT_HEADER, false);
         curl_setopt($ch, CURLOPT_TIMEOUT, 30);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_close($ch);

         $oUser = json_decode(curl_exec($ch));
        //  Log::info($oUser);
         
         if (!isset($oUser)) {
            print "Error accessing ".self::$pmServer .": \n" . curl_error($ch);
            return false;
         }
         elseif (isset($oUser->error)) {
            print "Error in ".self::$pmServer.": \nCode: {$oUser->error->code}\nMessage: {$oUser->error->message}\n";
            return false;
         }
         else {
            return $oUser;
            print "User '{$oUser->usr_username}' created with UID: {$oUser->usr_uid}\n";
         }
    }
}
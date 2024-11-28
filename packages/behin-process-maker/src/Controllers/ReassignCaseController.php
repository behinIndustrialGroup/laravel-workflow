<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mkhodroo\PMReport\Controllers\TableController;

class ReassignCaseController extends Controller
{

    public static function reassign($app_uid, $usr_uid_source, $usr_uid_target)
    {        
        $inbox =  CurlRequestController::put(
            AuthController::getAccessToken(),
            "/api/1.0/workflow/cases/$app_uid/reassign-case",
            array(
                'usr_uid_source' => $usr_uid_source,
                'usr_uid_target' => $usr_uid_target
            )
        );
        return $inbox;
    }

}
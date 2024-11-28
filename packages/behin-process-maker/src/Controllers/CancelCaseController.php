<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mkhodroo\PMReport\Controllers\TableController;

class CancelCaseController extends Controller
{

    public static function cancel($app_uid)
    {        
        $inbox =  CurlRequestController::post(
            AuthController::getAccessToken(),
            "/api/1.0/workflow/light/cases/$app_uid/cancel"
        );
        return $inbox;
    }

}
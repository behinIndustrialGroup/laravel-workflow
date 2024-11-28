<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mkhodroo\PMReport\Controllers\TableController;

class ClaimCaseController extends Controller
{

    public static function claim($app_uid)
    {        
        $inbox =  CurlRequestController::post(
            AuthController::getAccessToken(),
            "/api/1.0/workflow/light/case/$app_uid/claim"
        );
        return $inbox;
    }

    function form()
    {
        return view('PMViews::todo');
    }
}
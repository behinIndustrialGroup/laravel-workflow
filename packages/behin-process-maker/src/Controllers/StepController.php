<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class StepController extends Controller
{
    

    public static function list($processId, $taskId, $accessToken = null){
        if(!$accessToken){
            $accessToken = AuthController::getAccessToken();
        }
        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/project/$processId/activity/$taskId/steps"
        );
    }
}
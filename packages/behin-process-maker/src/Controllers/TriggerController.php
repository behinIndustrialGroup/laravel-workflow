<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;

class TriggerController extends Controller
{
    public static function listOfRoutingTriggers($processId, $taskId){
        $accessToken = AuthController::getAccessToken();

        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/project/$processId/activity/$taskId/step/triggers"
        );
    }
    
    public static function excute($triggerId, $caseId, $accessToken = null) {
        if(!$accessToken){
            $accessToken = AuthController::getAccessToken();
        }
        $result =  CurlRequestController::put(
            $accessToken, 
            "/api/1.0/workflow/cases/$caseId/execute-trigger/$triggerId"
        );
        return $result;
    }

    public static function getAvalableTrrigersBeforeTask($processId, $taskId, $caseId){
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/project/$processId/activity/$taskId/step/$caseId/available-triggers/before"
        );
    }
}
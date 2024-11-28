<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class GetTaskAsigneeController extends Controller
{
    public static function getAssignees($processId, $taskId) {
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/project/$processId/case-tracker/property"
        );
    }

    public static function getAvailableAssignees($processId, $taskId) {
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/project/$processId/activity/$taskId/available-assignee"
        );
    }

    public static function listOfTaskAssignee($processId, $taskId) {
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/project/$processId/activity/$taskId/assignee"
        );
    }
}
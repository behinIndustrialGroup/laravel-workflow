<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\AuthController;
use BehinProcessMaker\Controllers\CaseController;
use BehinProcessMaker\Controllers\CaseTrackerController;
use BehinProcessMaker\Controllers\CurlRequestController;
use BehinProcessMaker\Controllers\GetCaseVarsController;
use BehinProcessMaker\Controllers\GetTaskAsigneeController;
use BehinProcessMaker\Controllers\ReassignCaseController as ControllersReassignCaseController;
use BehinProcessMaker\Controllers\TaskController;
use BehinProcessMaker\Models\PmVars;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use SoapClient;

class ReassignCaseController extends Controller
{
    public static function reassign(Request $r){
        $sessionId = AuthController::wsdl_login()->message;
        $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')) . '/sysworkflow/en/green/services/wsdl2');
        $params = array(array(
            'sessionId' => $sessionId, 
            'caseId' => $r->caseId, 
            'delIndex' => $r->delIndex,
            'userIdSource' => '884962377668917c92d7603066296900',
            'userIdTarget' => '00000000000000000000000000000001'
        ));
        $result = $client->__SoapCall('reassignCase', $params);
        if ($result->status_code != 0)
            return response($result->message, 400);

        return response("ok", 200);
        return ControllersReassignCaseController::reassign($r->caseId, '884962377668917c92d7603066296900', '00000000000000000000000000000001');
    }

}
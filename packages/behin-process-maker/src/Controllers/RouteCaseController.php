<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use SoapClient;

class RouteCaseController extends Controller
{
    public static function next($app_uid, $del_index)
    {
        // $sessionId = AuthController::wsdl_login()->message;
        // $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')) . '/sysworkflow/en/green/services/wsdl2');
        // $params = array(array(
        //     'sessionId' => $sessionId,
        //     'caseId' => $app_uid, 'delIndex' => $del_index
        // ));
        // $result = $client->__SoapCall('routeCase', $params);
        // if ($result->status_code == 0)
        //     return "ok";
        // else
        //     Log::info("Error deriving case: $result->message \n");

        $accessToken = AuthController::getAccessToken();
        $result = CurlRequestController::put(
            $accessToken, 
            "/api/1.0/workflow/cases/$app_uid/route-case",
            array(
                "del_index" => $del_index
            )
        );

        return $result;
    }
}

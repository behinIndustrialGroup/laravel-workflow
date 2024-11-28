<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use BehinProcessMaker\Models\PMCase;
use SoapClient;

class CaseController extends Controller
{
    public static function getCaseInfo($caseId, $delIndex = 1){
        $sessionId = AuthController::wsdl_login()->message;
        $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')). '/sysworkflow/en/green/services/wsdl2');
        
        $params = array(array('sessionId'=>$sessionId, 'caseId' => $caseId, 'delIndex'=> $delIndex));
        $result = $client->__SoapCall('getCaseInfo', $params);
        return $result;
    }

    public static function saveToDb($processId, $caseId, $caseName){
        return PMCase::updateOrCreate(
            [
                'case_id' => $caseId,
                'process_id' => $processId
            ],
            [
                'case_name' => $caseName
            ]
            );
    }
}


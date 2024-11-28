<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class ProcessController extends Controller
{
    
    public static function getNameById($process_id) {
        $sessionId = AuthController::wsdl_login()->message;
        $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')). '/sysworkflow/en/green/services/wsdl2');
        
        $params = array(array('sessionId'=>$sessionId));
        $result = $client->__SoapCall('processList', $params);
        return collect($result->processes)->where('guid', $process_id)->first();
    }
}
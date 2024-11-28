<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class NewCaseController extends Controller
{
    private $accessToken;

    public function __construct()
    {
    }

    public static function create(Request $r)
    {
        $sessionId = AuthController::wsdl_login()->message;
        $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')) . '/sysworkflow/en/green/services/wsdl2');
        $name = new variableStruct();
        $vars = array(
            'crm_user_creator' => Auth::user()->id
        );
        $aVars = array();
        foreach ($vars as $key => $val) {
            $obj = new variableStruct();
            $obj->name = $key;
            $obj->value = $val;
            $aVars[] = $obj;
        }
        $params = array(
            array(
                'sessionId' => $sessionId,
                'processId' => $r->processId,
                'taskId' => $r->taskId,
                'variables' => $aVars
            )
        );
        $result = $client->__SoapCall('newCase', $params);
        return $result;
    }

    function form()
    {
        return view('PMViews::start');
    }
}

class variableStruct
{
    public $name;
    public $value;
}

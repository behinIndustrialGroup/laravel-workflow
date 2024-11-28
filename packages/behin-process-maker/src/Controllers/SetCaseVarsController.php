<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;

class SetCaseVarsController extends Controller
{
    private $accessToken;
    private static $system_vars;

    public function __construct() {}
    function saveAndNext(Request $r)
    {
        $save = $this->save($r);
        if(!is_array($save)){
            return $save;
        }

        // $system_vars = (new GetCaseVarsController())->getByCaseId($r->caseId);
        $result = DynaFormTriggerController::executeAfterDynaformTriggers($r->processId, $r->taskId, $r->caseId);
        if ($result) {
            return response($result, 402);
        }

        $route = RouteCaseController::next($r->caseId, $r->del_index);
        //ارسال پیامک برای یوزر بعدی 
        //در صورتی که یوزر بعدی مشخص باشد
        if (config('pm_config.send_sms_to_next_user')) {
            SendSmsController::toNextUser($r->caseId, $r->del_index);
        }

        //همگام سازی متغیرهای لوکال با متغیرهای روی پراسس میکر
        SyncVarsController::syncServerWithLocal($r->processId, $r->caseId);

        return true;
    }

    function save(Request $r)
    {
        $r->validate([
            'processId' => 'required',
            'taskId' => 'required',
            'caseId' => 'required',
        ]);
        // self::getVariableFromPmServer($r->caseId);
        //اجرای تریگرهای بعد از دینامیک فرم در هنگام ذخیره فرم
        // در صورتی که در فایل کانفیگ مقدار درست ذخیره شده باشد
        if (config('pm_config.execute_after_dynaform_triggers_in_save_form')) {
            $result = DynaFormTriggerController::executeAfterDynaformTriggers($r->processId, $r->taskId, $r->caseId);
            if ($result) {
                return response($result, 402);
            }
        }
        $sessionId = AuthController::wsdl_login()->message;
        $client = new SoapClient(str_replace('https', 'http', env('PM_SERVER')) . '/sysworkflow/en/green/services/wsdl2');
        $vars = $r->except(
            'caseId',
            'SYS_LANG',
            'SYS_SKIN',
            'SYS_SYS',
            'APPLICATION',
            'PROCESS',
            'TASK',
            'INDEX',
            'USER_LOGGED',
            'USR_USERNAME',
            'APP_NUMBER',
            'PIN'
        );
        $variables = array();
        $local_fields = GetCaseVarsController::getVarsFromLocal($r->processId, $r->caseId);
        foreach ($vars as $key => $val) {
            if (gettype($val) == 'object') {
                $field_name = explode("-", $key)[0];
                $fileId = explode("-", $key)[1];
                InputDocController::upload($r->file($key), $r->taskId, $r->caseId, $fileId, self::$system_vars->USER_LOGGED, $field_name);
            } elseif (gettype($val) == 'array') {
                foreach ($val as $pic) {
                    $saveDoc = SaveVarsController::saveDoc($r->processId, $r->caseId, $key, $pic);
                    if ($saveDoc) {
                        return $saveDoc;
                    }
                }
            } else {
                $obj = new variableListStruct();
                $obj->name = $key;
                $obj->value = $val;
                $variables[] = $obj;
                SaveVarsController::save($r->processId, $r->caseId, $key, $val);
            }
        }
        $params = array(array('sessionId' => $sessionId, 'caseId' => $r->caseId, 'variables' => $variables));
        $result = $client->__SoapCall('sendVariables', $params);
        if ($result->status_code != 0)
            return response($result->message, 400);

        return [
            'code' => 200,
            'message' => 'ok'
        ];
    }

    public static function getVariableFromPmServer($caseId)
    {
        self::$system_vars = (new GetCaseVarsController())->getByCaseId($caseId);
    }
}

class variableListStruct
{
    public $name;
    public $value;
}

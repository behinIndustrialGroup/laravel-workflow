<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SyncVarsController extends Controller
{
    public static function syncServerWithLocal($processId, $caseId) {
        $getCase = new GetCaseVarsController();
        $vars = $getCase->getByCaseId($caseId);
        foreach($vars as $key => $value){
            if(!in_array($key, [ 'USR_USERNAME', 'USER_LOGGED' ])){
                if(gettype($value) != 'string'){
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE );
                }
                SaveVarsController::save($processId, $caseId, $key, $value);
            }
        }
    }
}

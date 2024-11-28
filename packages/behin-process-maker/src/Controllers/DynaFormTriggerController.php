<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use SoapClient;

class DynaFormTriggerController extends Controller
{
    public static function executeAfterDynaformTriggers($processId, $taskId, $caseId){
        $steps = StepController::list($processId, $taskId);
        foreach ($steps as $step) {
            $triggers = $step->triggers;
            foreach ($triggers as $trigger) {
                if ($trigger->st_type === "AFTER") {
                    $result = TriggerController::excute($trigger->tri_uid, $caseId);
                    if($result?->original){
                        Log::info("Trigger Executed");
                        Log::info($trigger->tri_uid);
                        $result = iconv("UTF-8", "ISO-8859-1", $result->original);
                        $resultText = str_replace("Bad Request: ", "", $result);
                        $resultText = trans($resultText);
                        return $resultText;
                    }
                }
            }
        }
    }
    
}
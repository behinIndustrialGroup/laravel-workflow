<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMakerAdmin\Models\PmVars;

class GetCaseController extends Controller
{
    public static function getCaseRowsFromLocalDb($processId, $caseId){
        return PmVars::where('process_id', $processId)->where('case_id', $caseId)->get();
    }
}
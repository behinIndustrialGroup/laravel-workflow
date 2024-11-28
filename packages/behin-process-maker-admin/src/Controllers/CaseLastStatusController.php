<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\PMUserController;
use BehinProcessMaker\Models\PmVars;

class CaseLastStatusController extends Controller
{

    public static function getAllStatus(){
        $last_statuses = PmVars::where('key', 'last_status')->groupBy('value')->get();
        return $last_statuses;
    }
}

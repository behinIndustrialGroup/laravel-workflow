<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\PMUserController;
use BehinProcessMaker\Models\PmVars;

class CaseRepairmanController extends Controller
{

    public static function getAllRepairman(){
        $repairmans = PmVars::where('key', 'repairman')->groupBy('value')->get()->each(function($row){
            $row->name = PMUserController::getUserByPmUserId($row->value)?->name;
        });
        return $repairmans;
    }
}

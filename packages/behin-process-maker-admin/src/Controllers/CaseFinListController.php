<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\PMUserController;
use BehinProcessMaker\Models\PmVars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaseFinListController extends Controller
{
    public function finListView(){
        return view('PMAdminViews::fin-report.list')->with([
            'last_statuses' => CaseLastStatusController::getAllStatus()
        ]);
    }

    public static function getData(Request $request){
        $last_status = $request->last_status;
        $repair_report = $request->repair_report;
        $repairman = $request->repairman;

        $searchQuery = DB::table('pm_vars')
            ->select(
                'case_id',
                'process_id',
                DB::raw("MAX(CASE WHEN `key` = 'last_status' THEN value END) as last_status"),
                DB::raw("MAX(CASE WHEN `key` = 'customer_fullname' THEN value END) as customer_fullname"),
                DB::raw("MAX(CASE WHEN `key` = 'case_number' THEN value END) as case_number"),
                DB::raw("MAX(CASE WHEN `key` = 'receive_date' THEN value END) as receive_date"),
                DB::raw("MAX(CASE WHEN `key` = 'device_name' THEN value END) as device_name"),
                DB::raw("MAX(CASE WHEN `key` = 'repairman' THEN value END) as repairman"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_cost' THEN value END) as repair_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_report' THEN value END) as repair_report"),
            )
            ->groupBy('case_id');
        if($last_status){
            $searchQuery = $searchQuery->having('last_status', $last_status);
        }
        // if($repair_report){
        //     $searchQuery = $searchQuery->having('repair_report', 'like', '%' .$repair_report . '%');
        // }
        // if($repairman){
        //     $searchQuery = $searchQuery->having('repairman', $repairman);
        // }
        $searchResults = $searchQuery->get()->each(function($row){
            $row->repairman_name = PMUserController::getUserByPmUserId($row->repairman)?->name;
        });
        return $searchResults;
    }
}

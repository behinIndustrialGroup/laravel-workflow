<?php

namespace BehinProcessMakerAdmin\Controllers;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\PMUserController;
use BehinProcessMaker\Models\PmVars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaseReportController extends Controller
{
    public function casesByLastStatusView()
    {
        return view('PMAdminViews::cases-by-last-status.list')->with([
            'last_statuses' => CaseLastStatusController::getAllStatus()
        ]);
    }

    public static function cerateVirtualTable()
    {
        $searchQuery = DB::table('pm_vars')
            ->select(
                'case_id',
                'process_id',
                DB::raw("MAX(CASE WHEN `key` = 'last_status' THEN value END) as last_status"),
                DB::raw("MAX(CASE WHEN `key` = 'customer_fullname' THEN value END) as customer_fullname"),
                DB::raw("MAX(CASE WHEN `key` = 'customer_national_id' THEN value END) as customer_national_id"),
                DB::raw("MAX(CASE WHEN `key` = 'customer_mobile' THEN value END) as customer_mobile"),
                DB::raw("MAX(CASE WHEN `key` = 'receive_date' THEN value END) as receive_date"),
                DB::raw("MAX(CASE WHEN `key` = 'device_name' THEN value END) as device_name"),
                DB::raw("MAX(CASE WHEN `key` = 'repairman' THEN value END) as repairman"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_cost' THEN value END) as repair_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_report' THEN value END) as repair_report"),
            )
            ->groupBy('case_id');
        $searchQuery = DB::table(DB::raw("({$searchQuery->toSql()}) as sub"))
            ->mergeBindings($searchQuery);
        return $searchQuery;
    }

    public static function numberOfCaseByCustomerView(){
        return view('PMAdminViews::report.by-customer')->with([
            'data' => self::numberOfCaseByCustomer()
        ]);
    }

    public static function numberOfCaseByCustomer()
    {
        $searchQuery = self::cerateVirtualTable();
        return $searchQuery->select(
            'customer_national_id',
            'customer_fullname',
            DB::raw('COUNT(*) as total_records')  // شمارش رکوردها
        )->groupBy('customer_national_id')->orderBy('total_records', 'desc')->get();
    }

    public static function numberOfCaseByLastStatus()
    {
        $searchQuery = self::cerateVirtualTable();
        return $searchQuery->select(
            'last_status',
            DB::raw('COUNT(*) as total_records')  // شمارش رکوردها
        )->groupBy('last_status')->orderBy('total_records', 'desc')->get()->toArray();
    }
}

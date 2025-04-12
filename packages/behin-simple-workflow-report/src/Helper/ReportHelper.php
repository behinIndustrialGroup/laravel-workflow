<?php

namespace Behin\SimpleWorkflowReport\Helper;

use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class ReportHelper
{
    public static function getFilteredFinTable($year = null, $month = null)
    {
        $mapaSubquery = DB::table('wf_variables')
            ->select('case_id', DB::raw('MAX(value) as mapa_expert_id'))
            ->where('key', 'mapa_expert')
            ->groupBy('case_id');

        $query = DB::table('wf_variables')
            ->join('wf_cases', 'wf_variables.case_id', '=', 'wf_cases.id')
            ->leftJoinSub($mapaSubquery, 'mapa', function ($join) {
                $join->on('wf_variables.case_id', '=', 'mapa.case_id');
            })
            ->leftJoin('users', 'mapa.mapa_expert_id', '=', 'users.id')
            ->leftJoin('wf_process', 'wf_cases.process_id', '=', 'wf_process.id')
            ->select(
                'wf_variables.case_id',
                'wf_cases.number',
                'wf_process.name as process_name',
                'wf_process.id as process_id',
                DB::raw("MAX(CASE WHEN `key` = 'customer_workshop_or_ceo_name' THEN `value` ELSE '' END) AS customer"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_cost' THEN `value` ELSE 0 END) AS repair_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_cost' THEN `value` ELSE 0 END) AS fix_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'payment_amount' THEN `value` ELSE 0 END) AS payment_amount"),
                DB::raw("MAX(CASE WHEN `key` = 'visit_date' THEN `value` ELSE 0 END) AS visit_date"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_report' THEN wf_variables.updated_at ELSE null END) AS fix_report_date"),
                'users.name as mapa_expert_name',
                'users.id as mapa_expert_id'
            )
            ->groupBy('wf_variables.case_id')
            ->havingRaw('mapa_expert_id is not null');

        if ($year && $month) {
            // تاریخ شروع ماه شمسی
            $from = Jalalian::fromFormat('Y-m-d', "$year-$month-01")->toCarbon()->startOfDay();
            // تاریخ پایان ماه شمسی
            $to = Jalalian::fromFormat('Y-m-d', "$year-$month-01")->addMonths(1)->subDays(1)->toCarbon()->endOfDay();

            $query->whereExists(function ($subQuery) use ($from, $to) {
                $subQuery->select(DB::raw(1))
                    ->from('wf_variables as vars')
                    ->whereColumn('vars.case_id', 'wf_variables.case_id')
                    ->where('vars.key', 'fix_report')
                    ->whereBetween('vars.updated_at', [$from, $to]);
            });
        }

        if ($year && !$month) {
            $from = Jalalian::fromFormat('Y-m-d', "$year-01-01")->toCarbon()->startOfDay();
            $to = Jalalian::fromFormat('Y-m-d', "$year-12-29")->toCarbon()->endOfDay();

            $query->whereExists(function ($subQuery) use ($from, $to) {
                $subQuery->select(DB::raw(1))
                    ->from('wf_variables as vars')
                    ->whereColumn('vars.case_id', 'wf_variables.case_id')
                    ->where('vars.key', 'fix_report')
                    ->whereBetween('vars.updated_at', [$from, $to]);
            });
        }

        return $query->get();
    }
}
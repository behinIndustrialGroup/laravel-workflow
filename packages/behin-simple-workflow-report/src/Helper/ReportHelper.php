<?php

namespace Behin\SimpleWorkflowReport\Helper;

use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class ReportHelper
{
    public static function getFilteredFinTable($from, $to= null, $user = null)
    {
        $from = convertPersianToEnglish($from);
        $to = convertPersianToEnglish($to);
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
                DB::raw("MAX(CASE WHEN `key` = 'payment_date' THEN `value` END) AS payment_date"),
                DB::raw("MAX(CASE WHEN `key` = 'visit_date' THEN `value` ELSE 0 END) AS visit_date"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_report' THEN UNIX_TIMESTAMP(wf_variables.updated_at) ELSE null END) AS fix_report_date"),
                'users.name as mapa_expert_name',
                'users.id as mapa_expert_id'
            )
            ->groupBy('wf_variables.case_id')
            ->whereNull('wf_cases.deleted_at')
            ->havingRaw('mapa_expert_id is not null');

        if ($user) {
            $query->havingRaw('mapa_expert_id = ?', [$user]);
        }



        if ($from && $to) {
            $from = Jalalian::fromFormat('Y-m-d', $from)->toCarbon()->startOfDay()->timestamp;
            $to = Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay()->timestamp;

            $query->havingRaw('fix_report_date BETWEEN ? AND ?', [$from, $to]);
        }

        return $query->get();
    }
}

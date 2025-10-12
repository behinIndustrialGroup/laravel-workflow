<?php

namespace Behin\SimpleWorkflowReport\Helper;

use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Case_costs;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;

class ReportHelper
{
    public static function getFilteredFinTable($from, $to= null, $user = null)
    {
        $from = convertPersianToEnglish($from);
        $to = convertPersianToEnglish($to);
        $from = Jalalian::fromFormat('Y-m-d', $from)->toCarbon()->startOfDay()->timestamp;
        $to = Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay()->timestamp;
        $inFins = Financials::rightJoin('wf_entity_parts', 'wf_entity_parts.case_number', '=', 'wf_entity_financials.case_number')
        ->whereBetween('wf_entity_financials.fix_cost_date', [$from, $to]);
        if ($user) {
            $counterParties = Counter_parties::where('user_id', $user)->pluck('id');
            $inFins = $inFins->where(function ($query) use ($user) {
                $query->where('wf_entity_parts.mapa_expert', $user);
            });
        }
        $inFins = $inFins->select('wf_entity_financials.*', 'wf_entity_parts.mapa_expert as in_mapa_expert')
        ->groupBy('wf_entity_financials.case_number', 'wf_entity_financials.process_id')
        ->get();

        $outFins = Financials::rightJoin('wf_entity_repair_reports', 'wf_entity_repair_reports.case_number', '=', 'wf_entity_financials.case_number')
        ->whereBetween('wf_entity_financials.fix_cost_date', [$from, $to]);
        if ($user) {
            $outFins = $outFins->where(function ($query) use ($user) {
                $query->where('wf_entity_repair_reports.mapa_expert', $user);
            });
        }
        $outFins = $outFins->select('wf_entity_financials.*', 'wf_entity_repair_reports.mapa_expert as out_mapa_expert')
        ->groupBy('wf_entity_financials.case_number', 'wf_entity_financials.process_id')
        ->get();
        $fins = $inFins->merge($outFins);
        foreach ($fins as $fin) {
            // $fin->total_cost = Financials::where('case_number', $fin->case_number)->get()?->sum('cost');
            
            try {
                $fin->total_cost = Financials::where('case_number', $fin->case_number)->get()?->sum(fn($item) => (float) $item->cost);
            } catch (\Throwable $e) {
                $fin->total_cost = 'Error '. $fin->case_number;
                dd('Error in total_cost', $fin->case_number);
            }
            try {
                $fin->total_payment = Financials::where('case_number', $fin->case_number)->get()?->sum(fn($item) => (float) $item->payment);
            } catch (\Throwable $e) {
                $fin->total_cost = 'Error '. $fin->case_number;
                dd('Error in total_cost', $fin->case_number);
            }
            $fin->in_mapa_experts = Parts::where('case_number', $fin->case_number)->groupBy('mapa_expert')->pluck('mapa_expert')->toArray();
            $fin->out_mapa_experts = Repair_reports::where('case_number', $fin->case_number)->groupBy('mapa_expert')->pluck('mapa_expert')->toArray();
            $fin->customer = Variable::where('case_id', $fin->case_id)->where('key', 'customer_workshop_or_ceo_name')->value('value');
            if($user){
                $fin->case_costs = Case_costs::where('case_number', $fin->case_number)->whereIn('couterparty', $counterParties)->get();
                $fin->all_case_costs = Case_costs::where('case_number', $fin->case_number)->get();
            }else{
                $fin->case_costs = Case_costs::where('case_number', $fin->case_number)->get();
            }
        }
        $fins = $fins->unique('case_number')->values();
        return $fins;


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
            ->leftJoin('wf_entity_financials', 'wf_cases.number', '=', 'wf_entity_financials.case_number')
            ->select(
                'wf_variables.case_id',
                'wf_cases.number',
                'wf_process.name as process_name',
                'wf_process.id as process_id',
                DB::raw("MAX(CASE WHEN `key` = 'customer_workshop_or_ceo_name' THEN `value` ELSE '' END) AS customer"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_cost' THEN `value` ELSE 0 END) AS repair_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_cost' THEN `value` ELSE 0 END) AS fix_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_cost_2' THEN `value` ELSE 0 END) AS fix_cost_2"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_cost_3' THEN `value` ELSE 0 END) AS fix_cost_3"),
                DB::raw("MAX(CASE WHEN `key` = 'payment_amount' THEN `value` ELSE 0 END) AS payment_amount"),
                DB::raw("MAX(CASE WHEN `key` = 'payment_date' THEN `value` END) AS payment_date"),
                DB::raw("MAX(CASE WHEN `key` = 'visit_date' THEN `value` ELSE 0 END) AS visit_date"),
                DB::raw("MAX(CASE WHEN `key` = 'fix_report' THEN UNIX_TIMESTAMP(wf_variables.updated_at) ELSE null END) AS fix_report_date"),
                'users.name as mapa_expert_name',
                'users.id as mapa_expert_id',
                'wf_entity_financials.cost as financial_cost',
                'wf_entity_financials.cost2 as financial_cost2',
                'wf_entity_financials.cost3 as financial_cost3',
                'wf_entity_financials.payment as financial_payment',
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

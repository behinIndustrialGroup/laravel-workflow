<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Case_costs;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;

use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class FinReportController extends Controller
{
    public function index(Request $request)
    {
        return view('SimpleWorkflowReportView::Core.Summary.process.partial.fin-reports');
        $vars = VariableController::getAll($fields = ['case_number', 'customer_fullname', 'receive_date', 'device_name', 'repairman', 'payment_amount', 'last_status']);
        $statuses = Variable::where('key', 'last_status')->groupBy('value')->get();
        $repairmans = Variable::where('key', 'repairman')->groupBy('value')->get();
        return view('SimpleWorkflowReportView::Core.Fin.index', compact('vars', 'statuses', 'repairmans'));
    }

    public function totalCost(Request $request)
    {
        $today = Carbon::today();
        $todayShamsi = Jalalian::fromCarbon($today);
        $thisYear = $todayShamsi->getYear();
        $thisMonth = $todayShamsi->getMonth();
        $thisMonth = str_pad($thisMonth, 2, '0', STR_PAD_LEFT);
        $to = Jalalian::fromFormat('Y-m-d', "$thisYear-$thisMonth-01")
            ->addMonths(1)
            ->subDays(1)
            ->format('Y-m-d');

        $from = isset($request->from) ? $request->from : "$thisYear-$thisMonth-01";
        $to = isset($request->to) ? $request->to : (string) $to;
        $from = convertPersianToEnglish($from);
        $to = convertPersianToEnglish($to);
        $fromTimestamp = Jalalian::fromFormat('Y-m-d', $from)->toCarbon()->startOfDay()->timestamp;
        $toTimestamp = Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay()->timestamp;

        $user = $request->quser ?? null;
        if($user){
            $userCounterParties = Counter_parties::where('user_id', $user)->pluck('id');
        }else{
            $userCounterParties = [];
        }
        $autoTransactionIds = Financial_transactions::whereNotNull('auto_financial_transaction_id')->pluck('auto_financial_transaction_id');
        $finTable = DB::table('wf_cases as c')
            ->leftJoin('wf_entity_financial_transactions as ft', 'c.number', '=', 'ft.case_number')
            //فرایند داخلی و خارجی
            ->whereIn('c.process_id', [
                '35a5c023-5e85-409e-8ba4-a8c00291561c', //خارجی
                '4bb6287b-9ddc-4737-9573-72071654b9de', //داخلی
            ])
            ->whereNotNull('c.number')
            ->where('c.number', '!=', '')
            ->when($fromTimestamp && $toTimestamp, function ($q) use ($fromTimestamp, $toTimestamp) {
                $q->whereRaw("
                    CASE
                        WHEN ft.transaction_or_cheque_due_date_alt IS NOT NULL
                            AND ft.transaction_or_cheque_due_date_alt != 0
                        THEN ft.transaction_or_cheque_due_date_alt / 1000
                        ELSE UNIX_TIMESTAMP(ft.updated_at)
                    END BETWEEN ? AND ?
                ", [$fromTimestamp, $toTimestamp]);
            })
            
            ->select(
                'c.number',
                'c.id',
                'c.process_id',
                'ft.transaction_or_cheque_due_date as date',
                'ft.amount as amount',
                'ft.financial_type',
                DB::raw("
                    CASE 
                        WHEN ft.financial_type = 'بدهکار' 
                        THEN ft.amount 
                        ELSE 0 
                    END AS debt_amount
                ")
            )
            ->groupBy('c.number')
            ->get()->each(function ($item) use ($user, $userCounterParties, $autoTransactionIds) {
                $item->customer = Variable::where('case_id', $item->id)->where('key', 'customer_workshop_or_ceo_name')->value('value');
                $item->process_name = Process::find($item->process_id)->name ?? '';
                $inExperts = Parts::where('case_number', $item->number)->groupBy('mapa_expert')->pluck('mapa_expert')->toArray();
                $outExperts = Repair_reports::where('case_number', $item->number)->groupBy('mapa_expert')->pluck('mapa_expert')->toArray();
                $item->experts = array_merge($inExperts, $outExperts);

                if($user){
                    $item->case_costs = Case_costs::where('case_number', $item->number)->whereIn('couterparty', $userCounterParties)->get();
                } else {
                    $item->case_costs = Case_costs::where('case_number', $item->number)->get();
                }

                $item->all_case_costs = Case_costs::where('case_number', $item->number)->get();

                $item->debtDate = Financial_transactions::where('case_number', $item->number)
                    ->where('financial_type', 'بدهکار')->first();
                if ($item->debtDate and $item->debtDate->transaction_or_cheque_due_date_alt) {
                    $item->debtDate = $item->debtDate->transaction_or_cheque_due_date_alt / 1000;
                } elseif ($item->debtDate and $item->debtDate->updated_at) {
                    $item->debtDate = $item->debtDate->updated_at->timestamp;
                } else {
                    $item->debtDate = null;
                }

                $item->case_debts = Financial_transactions::where('case_number', $item->number)
                    ->where('financial_type', 'بدهکار')
                    ->whereNotIn('id', $autoTransactionIds)
                    ->select('amount')->get();

                $item->paymentDate = Financial_transactions::where('case_number', $item->number)
                    ->where('financial_type', 'بستانکار')->first();
                if ($item->paymentDate and $item->paymentDate->transaction_or_cheque_due_date_alt) {
                    $item->paymentDate = $item->paymentDate->transaction_or_cheque_due_date_alt / 1000;
                } elseif ($item->paymentDate and $item->paymentDate->updated_at) {
                    $item->paymentDate = $item->paymentDate->updated_at->timestamp;
                } else {
                    $item->paymentDate = null;
                }

                $item->payments = Financial_transactions::where('case_number', $item->number)
                    ->where('financial_type', 'بستانکار')
                    ->whereNotIn('id', $autoTransactionIds)
                    ->select('amount')->get();
            });
        // dd($finTable);
        return view('SimpleWorkflowReportView::Core.Summary.process.partial.total-cost', compact('finTable'));
    }

    public function totalPayment()
    {
        $vars = VariableController::getAll($fields = ['payment_amount'])->pluck('payment_amount');
        $sum = 0;
        $ar = [];
        foreach ($vars as $var) {
            $var = str_replace(',', '', $var);
            $var = str_replace(' ', '', $var);
            $var = str_replace('ریال', '', $var);
            $var = str_replace('تومان', '', $var);
            $var = str_replace('/', '', $var);
            $var = str_replace('.', '', $var);
            if (is_numeric($var)) {
                $sum += $var;
            }
            $ar[] = $var;
        }
        return $sum;
    }

    public static function allPayments(Request $request)
    {
        $user = $request->user;
        $from = convertPersianToEnglish($request->from);
        $to = convertPersianToEnglish($request->to);
        $today = Carbon::today();
        $todayShamsi = Jalalian::fromCarbon($today);
        $thisYear = $todayShamsi->getYear();
        $thisMonth = $todayShamsi->getMonth();
        $thisMonth = str_pad($thisMonth, 2, '0', STR_PAD_LEFT);
        $to = Jalalian::fromFormat('Y-m-d', "$thisYear-$thisMonth-01")
            ->addMonths(1)
            ->subDays(1)
            ->format('Y-m-d');

        $from = isset($request->from) ? convertPersianToEnglish($request->from) : "$thisYear-$thisMonth-01";
        $to = isset($request->to) ? convertPersianToEnglish($request->to) : (string) $to;

        $rows = Financials::select('*');

        if ($user) {
            $rows = $rows->where('destination_account_name', $user);
        }

        if ($from && $to) {
            $from = Jalalian::fromFormat('Y-m-d', $from)->toCarbon()->startOfDay()->timestamp;
            $to = Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay()->timestamp;

            $rows = $rows->whereBetween('payment_date', [$from, $to]);
        }


        $rows = [
            'rows' => $rows->get(),
            'destinations' => $rows->get()->groupBy('destination_account_name')
        ];

        return view('SimpleWorkflowReportView::Core.Summary.process.partial.all-payments', compact('rows'));
    }
}

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
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class ChequeReportController extends Controller
{
    public function index(Request $request)
    {
        $cheques = Financials::whereNotNull('case_number')->whereIn('fix_cost_type', [ 'تسویه کامل - چک', 'علی الحساب - چک' ])->get();
        return view('SimpleWorkflowReportView::Core.Cheque.index', compact('cheques'));
    }

    public function totalCost()
    {
        return view('SimpleWorkflowReportView::Core.Summary.process.partial.total-cost');
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

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class ExternalAndInternalReportController extends Controller
{
    public function index(Request $request)
    {
        $cases = Cases::whereIn('process_id', [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de'
        ])
        ->whereNull('parent_id')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('wf_inbox')
                ->whereColumn('wf_inbox.case_id', 'wf_cases.id')
                ->whereNotIn('status', ['done', 'doneByOther', 'canceled']);
        })
        ->groupBy('number')
        ->get();
        return view('SimpleWorkflowReportView::Core.ExternalInternal.index', compact('cases'));
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

        $rows = Financials::select('*');

        if ($user) {
            $rows = $rows->where('destination_account_name', $user);
        }

        if ($from && $to) {
            $from = Jalalian::fromFormat('Y-m-d', $from)->toCarbon()->startOfDay()->timestamp;
            $to = Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay()->timestamp;

            $rows = $rows->whereBetween('fix_cost_date', [$from, $to]);
        }


        $rows = [
            'rows' => $rows->get(),
            'destinations' => $rows->get()->groupBy('destination_account_name')
        ];

        return view('SimpleWorkflowReportView::Core.Summary.process.partial.all-payments', compact('rows'));
    }
}

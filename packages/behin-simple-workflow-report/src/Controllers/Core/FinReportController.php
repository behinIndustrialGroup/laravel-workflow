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
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function totalCost(){
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
        $year = $request->year;
        $month = $request->month;
        $rows = Financials::select('*');
        if($user){
            $rows = $rows->where('destination_account_name', $user);
        }
        if($year and $month){
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
            $startOfMonth = Jalalian::fromFormat('Y-m-d', "$year-$month-01")->toCarbon()->startOfDay()->timestamp;
            $endOfMonth = Jalalian::fromFormat('Y-m-d', "$year-$month-01")->addMonths(1)->subDays(1)->toCarbon()->endOfDay()->timestamp;
            $rows = $rows->where('fix_cost_date', '>=', $startOfMonth)->where('fix_cost_date', '<=', $endOfMonth);
            
        }
        $rows= [
            'rows' => $rows->get(),
            'destinations' => $rows->get()->groupBy('destination_account_name')
        ];
        return view('SimpleWorkflowReportView::Core.Summary.process.partial.all-payments', compact('rows'));
    }

}

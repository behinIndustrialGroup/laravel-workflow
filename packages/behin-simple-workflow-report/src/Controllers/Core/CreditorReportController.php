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
use Behin\SimpleWorkflow\Models\Entities\Creditor;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class CreditorReportController extends Controller
{
    public function index()
    {
        $creditors = Creditor::select('counterparty', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('counterparty')
            ->get();

        return view('SimpleWorkflowReportView::Core.Creditor.index', compact('creditors'));
    }

    public function show($counterparty)
    {
        $creditors = Creditor::where('counterparty', $counterparty)->get();
        return view('SimpleWorkflowReportView::Core.Creditor.show', compact('creditors'));
    }

    public function showAddTasvie($counterparty)
    {
        return view('SimpleWorkflowReportView::Core.Creditor.add-tasvie', compact('counterparty'));
    }

    public function addTasvie(Request $request)
    {
        $amount = str_replace(',', '', $request->amount);
        $amount = -1 * abs($amount);
        Creditor::create([
            'type' => 'تسویه',
            'description' => $request->description,
            'counterparty' => $request->counterparty,
            'amount' => (string)$amount,
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'invoice_date_alt' => $request->invoice_date_alt,
            'settlement_type' => $request->settlement_type,
        ]);
        return redirect()->route('simpleWorkflowReport.creditor.index');
    }

    public function showAddTalab($counterparty)
    {
        return view('SimpleWorkflowReportView::Core.Creditor.add-talab', compact('counterparty'));
    }

    public function addTalab(Request $request)
    {
        $amount = str_replace(',', '', $request->amount);
        $amount = abs($amount);
        Creditor::create([
            'type' => 'طلب',
            'description' => $request->description,
            'counterparty' => $request->counterparty,
            'amount' => (string)$amount,
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'invoice_date_alt' => $request->invoice_date_alt,
        ]);
        return redirect()->route('simpleWorkflowReport.creditor.index');
    }

    public function delete($id)
    {
        Creditor::destroy($id);
        return redirect()->route('simpleWorkflowReport.creditor.index');
    }
}

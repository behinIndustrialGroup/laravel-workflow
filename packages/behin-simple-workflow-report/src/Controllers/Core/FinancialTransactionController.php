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
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;

class FinancialTransactionController extends Controller
{
    public function index()
    {
        $creditors = Financial_transactions::select(
            'counterparty_id',
            DB::raw("SUM(CASE 
                WHEN financial_type = 'بدهکار' THEN -amount 
                WHEN financial_type = 'بستانکار' THEN amount 
                ELSE 0 
            END) as total_amount")
        )
            ->groupBy('counterparty_id')
            ->get();

        return view('SimpleWorkflowReportView::Core.FinancialTransaction.index', compact('creditors'));
    }


    public function show($counterparty)
    {
        $creditors = Financial_transactions::where('counterparty_id', $counterparty)->get();
        return view('SimpleWorkflowReportView::Core.FinancialTransaction.show', compact('creditors'));
    }

    public function showAddCredit($counterparty = null)
    {
        $counterparty = Counter_parties::find($counterparty);
        $counterParties = Counter_parties::all();
        return view('SimpleWorkflowReportView::Core.FinancialTransaction.add-credit', compact('counterparty', 'counterParties'));
    }

    public function addCredit(Request $request)
    {
        $amount = str_replace(',', '', $request->amount);
        return Financial_transactions::create([
            'case_number' => $request->case_number,
            'financial_type' => 'بستانکار',
            'financial_method' => $request->financial_method,
            'description' => $request->description,
            'counterparty_id' => $request->counterparty_id,
            'amount' => (string)$amount,
            'invoice_or_cheque_number' => $request->invoice_or_cheque_number,
            'transaction_or_cheque_due_date' => $request->transaction_or_cheque_due_date,
            'transaction_or_cheque_due_date_alt' => $request->transaction_or_cheque_due_date_alt,
            'destination_account_name' => $request->destination_account_name,
            'destination_account_number' => $request->destination_account_number,
        ]);
        return redirect()->route('simpleWorkflowReport.financial-transactions.index');
    }

    public function showAddDebit($counterparty = null)
    {
        $counterparty = Counter_parties::find($counterparty);
        $counterParties = Counter_parties::all();
        return view('SimpleWorkflowReportView::Core.FinancialTransaction.add-debit', compact('counterparty', 'counterParties'));
    }

    public function addDebit(Request $request)
    {
        $amount = str_replace(',', '', $request->amount);
        Financial_transactions::create([
            'case_number' => $request->case_number,
            'financial_type' => 'بدهکار',
            'financial_method' => $request->financial_method,
            'description' => $request->description,
            'counterparty_id' => $request->counterparty_id,
            'amount' => (string)$amount,
            'invoice_or_cheque_number' => $request->invoice_or_cheque_number,
            'transaction_or_cheque_due_date' => $request->transaction_or_cheque_due_date,
            'transaction_or_cheque_due_date_alt' => $request->transaction_or_cheque_due_date_alt,
            'destination_account_name' => $request->destination_account_name,
            'destination_account_number' => $request->destination_account_number,
        ]);
        return redirect()->route('simpleWorkflowReport.financial-transactions.index');
    }
}

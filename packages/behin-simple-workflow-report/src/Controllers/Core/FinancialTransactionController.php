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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;
use Illuminate\Validation\Rule;

class FinancialTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'negative');

        $totalAmountExpression = "SUM(CASE
                WHEN financial_type = 'بدهکار' THEN -amount
                WHEN financial_type = 'بستانکار' THEN amount
                ELSE 0
            END)";

        $creditorsQuery = Financial_transactions::select(
            'counterparty_id',
            DB::raw("{$totalAmountExpression} as total_amount")
        )
            ->groupBy('counterparty_id');

        switch ($filter) {
            case 'positive':
                $creditorsQuery->havingRaw("{$totalAmountExpression} > 0");
                break;
            case 'all':
                break;
            default:
                $filter = 'negative';
                $creditorsQuery->havingRaw("{$totalAmountExpression} < 0");
                break;
        }

        $creditors = $creditorsQuery->get();

        return view('SimpleWorkflowReportView::Core.FinancialTransaction.index', compact('creditors', 'filter'));
    }


    public function show($counterparty)
    {
        $creditors = Financial_transactions::where('counterparty_id', $counterparty)->get();
        return view('SimpleWorkflowReportView::Core.FinancialTransaction.show', compact('creditors'));
    }

    public function edit(Financial_transactions $financialTransaction)
    {
        $counterParties = Counter_parties::all();

        return view(
            'SimpleWorkflowReportView::Core.FinancialTransaction.edit',
            compact('financialTransaction', 'counterParties')
        );
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

    public function update(Request $request, Financial_transactions $financialTransaction)
    {
        $validated = $request->validate([
            'financial_type' => ['required', Rule::in(['بدهکار', 'بستانکار'])],
            'counterparty_id' => ['required'],
            'case_number' => ['nullable', 'string'],
            'amount' => ['required', 'string'],
            'financial_method' => ['nullable', 'string'],
            'invoice_or_cheque_number' => ['nullable', 'string'],
            'transaction_or_cheque_due_date' => ['nullable', 'string'],
            'transaction_or_cheque_due_date_alt' => ['nullable', 'string'],
            'destination_account_name' => ['nullable', 'string'],
            'destination_account_number' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $amount = str_replace(',', '', $validated['amount']);

        $financialTransaction->update([
            'financial_type' => $validated['financial_type'],
            'counterparty_id' => $validated['counterparty_id'],
            'case_number' => $validated['case_number'] ?? null,
            'amount' => (string) $amount,
            'financial_method' => $validated['financial_method'] ?? null,
            'invoice_or_cheque_number' => $validated['invoice_or_cheque_number'] ?? null,
            'transaction_or_cheque_due_date' => $validated['transaction_or_cheque_due_date'] ?? null,
            'transaction_or_cheque_due_date_alt' => $validated['transaction_or_cheque_due_date_alt'] ?? null,
            'destination_account_name' => $validated['destination_account_name'] ?? null,
            'destination_account_number' => $validated['destination_account_number'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('simpleWorkflowReport.financial-transactions.show', $financialTransaction->counterparty_id)
            ->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }

    public function destroy(Financial_transactions $financialTransaction): JsonResponse
    {
        $financialTransaction->delete();

        return response()->json([
            'message' => 'تراکنش با موفقیت حذف شد.',
        ]);
    }
}

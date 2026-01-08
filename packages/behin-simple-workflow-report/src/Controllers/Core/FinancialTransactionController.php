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
use Behin\SimpleWorkflowReport\Exports\UserFinancialTransactionExport;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;
use Behin\SimpleWorkflowReport\Exports\CounterpartyFinancialTransactionExport;
use Illuminate\Validation\Rule;

class FinancialTransactionController extends Controller
{
    public function prepareData($request)
    {
        $filter = $request->query('filter', 'negative');
        $caseNumber = $request->query('case_number');
        $onlyAssignedUsers = $request->boolean('only_assigned', false);

        $totalAmountExpression = "SUM(CASE
                WHEN financial_type = 'بدهکار' THEN -amount
                WHEN financial_type = 'بستانکار' THEN amount
                ELSE 0
            END)";

        $creditorsQuery = Financial_transactions::select(
            'counterparty_id',
            DB::raw("{$totalAmountExpression} as total_amount"),
        )
            ->when($caseNumber !== null && $caseNumber !== '', function ($query) use ($caseNumber) {
                $query->where('case_number', $caseNumber);
            })
            ->when($onlyAssignedUsers, function ($query) {
                $assignCounterParties = Counter_parties::whereNotNull('user_id')->pluck('id');
                $query->whereIn('counterparty_id', $assignCounterParties);
            }, function ($query) {
                // وقتی onlyAssignedUsers = false
                $unassignedCounterParties = Counter_parties::whereNull('user_id')->pluck('id');
                $query->whereIn('counterparty_id', $unassignedCounterParties);
            })
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

        return $creditorsQuery->get();
    }
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'negative');
        $caseNumber = $request->query('case_number');
        $onlyAssignedUsers = $request->boolean('only_assigned', false);
        $creditors = $this->prepareData($request);
        $counterpartyDebit = $creditors->where('total_amount', '<', 0)->sum(function ($item) {
            return abs($item->total_amount);
        });
        $counterpartyCredit = $creditors->where('total_amount', '>', 0)->sum('total_amount');
        $counterpartyBalance =  Financial_transactions::select(
            DB::raw("
            SUM(
                CASE
                    WHEN financial_type = 'بدهکار' THEN amount
                    ELSE 0
                END
            ) AS total_debit
        "),
            // جمع بستانکاری
            DB::raw("
            SUM(
                CASE
                    WHEN financial_type = 'بستانکار' THEN amount
                    ELSE 0
                END
            ) AS total_credit
        "),

            DB::raw("
        SUM(
            CASE
                WHEN financial_type = 'بستانکار' THEN amount
                ELSE -amount
            END
        ) AS balance
    ")
        )->groupBy('counterparty_id')->get();

        $totalDebit = $counterpartyBalance
            ->where('balance', '<', 0)
            ->sum(fn($item) => abs($item->balance));

        $totalCredit = $counterpartyBalance
            ->where('balance', '>', 0)
            ->sum('balance');


        $balance = Financial_transactions::select(
            DB::raw("
            SUM(
                CASE
                    WHEN financial_type = 'بدهکار' THEN -amount
                    WHEN financial_type = 'بستانکار' THEN amount
                    ELSE 0
                END
            ) AS total_amount
        "),
            // جمع بدهکاری
            DB::raw("
            SUM(
                CASE
                    WHEN financial_type = 'بدهکار' THEN amount
                    ELSE 0
                END
            ) AS total_debit
        "),
            // جمع بستانکاری
            DB::raw("
            SUM(
                CASE
                    WHEN financial_type = 'بستانکار' THEN amount
                    ELSE 0
                END
            ) AS total_credit
        ")
        )->first();

        return view(
            'SimpleWorkflowReportView::Core.FinancialTransaction.index',
            compact('creditors', 'filter', 'caseNumber', 'balance', 'counterpartyDebit', 'counterpartyCredit', 'counterpartyBalance', 'totalDebit', 'totalCredit')
        );
    }

    public function userIndex(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $caseNumber = $request->query('case_number');
        $onlyAssignedUsers = $request->boolean('only_assigned', true);
        $request->merge(['only_assigned' => true]);
        $request->merge(['filter' => 'all']);
        $counterParties = Counter_parties::whereNotNull('user_id')->get()->each(function ($row) {
            $row->user_max_advance = EmployeeSalaryReportController::userMaxAdvances($row->user_id);
        });

        $creditors = $this->prepareData($request);
        return view('SimpleWorkflowReportView::Core.UserFinancialTransaction.index', compact('creditors', 'filter', 'caseNumber', 'counterParties'));
    }

    public function openUserSalaryAdvances($counterparty)
    {
        $counterparty = Counter_parties::find($counterparty);
        if (!$counterparty->user_id) {
            return "برای این طرف حساب نمیتوانید حساب مساعده باز کنید";
        }

        // گرفتن مجموع تراکنش‌ها برای این طرف حساب
        $request = new Request([
            'filter' => 'all'
        ]);
        $creditors = $this->prepareData($request);

        // پیدا کردن رکورد این کاربر
        $creditor = $creditors->where('counterparty_id', $counterparty->id)->first();
        $totalAmount = $creditor ? $creditor->total_amount : 0;

        // اگر total_amount صفر نبود، عملیات انجام نشود
        if ($totalAmount != 0) {
            return redirect()->back()->with('error', 'برای این کاربر به دلیل داشتن مانده حساب، امکان باز کردن مساعده وجود ندارد.');
        }

        $userMaxAdvances = EmployeeSalaryReportController::userMaxAdvances($counterparty->user_id);
        $request = new Request([
            'financial_method' => 'نقدی',
            'description' => 'بازکردن مساعده',
            'counterparty_id' => $counterparty->id,
            'amount' => $userMaxAdvances
        ]);
        $this->addCredit($request);
        return redirect()->back();
    }

    public function openUserSalaryAdvancesBulk(Request $request)
    {
        $userIds = $request->users ?? [];

        if (empty($userIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'هیچ کاربری ارسال نشده است'
            ]);
        }

        foreach ($userIds as $id) {
            // فرض می‌کنیم روت تکی از یک تابع داخلی استفاده می‌کند
            $this->openUserSalaryAdvances($id);
        }

        return response()->json(['status' => 'ok']);
    }


    public function closeUserSalaryAdvances($counterparty)
    {
        $counterparty = Counter_parties::find($counterparty);
        if (!$counterparty->user_id) {
            return "برای این طرف حساب نمیتوانید حساب مساعده باز کنید";
        }
        $request = new Request([
            'filter' => 'all'
        ]);
        $creditors = $this->prepareData($request);
        $creditor = $creditors->where('counterparty_id', $counterparty->id);
        $totalAmount = $creditor->first() ? $creditor->first()->total_amount : 0;
        if ($totalAmount > 0) {
            $request = new Request([
                'financial_method' => 'نقدی',
                'description' => 'بستن مساعده',
                'counterparty_id' => $counterparty->id,
                'amount' => $totalAmount
            ]);
            $this->addDebit($request);
        }

        if ($totalAmount < 0) {
            $request = new Request([
                'financial_method' => 'نقدی',
                'description' => 'بستن مساعده',
                'counterparty_id' => $counterparty->id,
                'amount' => $totalAmount
            ]);
            $this->addCredit($request);
        }
        return redirect()->back();
    }

    public function userExport(Request $request)
    {
        $request->merge(['only_assigned' => true]);
        $request->merge(['filter' => 'all']);
        $counterParties = Counter_parties::whereNotNull('user_id')->get()->each(function ($row) {
            $row->user_max_advance = EmployeeSalaryReportController::userMaxAdvances($row->user_id);
        });
        $creditors = $this->prepareData($request);
        $data = [];
        foreach ($counterParties as $counterParty) {
            $creditorInfo = $creditors->where('counterparty_id', $counterParty->id);
            $totalAmount = $creditorInfo->first() ? $creditorInfo->first()->total_amount : 0;
            $rest = $counterParty->user_max_advance - $totalAmount;
            $data[] = [
                'user_number' => getUserInfo($counterParty->user_id)->number,
                'user_name' => $counterParty->name ?? '',
                'user_max_advance' => number_format($counterParty->user_max_advance),
                'total_amount' => number_format($totalAmount),
            ];
        }

        return Excel::download(new UserFinancialTransactionExport(collect($data)), 'user_financial_transactions.xlsx');
    }


    public function show(Request $request, $counterparty)
    {
        $showHeaderBtn = $request->input('showHeaderBtn', '1') == '1';
        $creditors = Financial_transactions::where('counterparty_id', $counterparty)->get();
        return view('SimpleWorkflowReportView::Core.FinancialTransaction.show', compact('creditors', 'showHeaderBtn'));
    }

    public function export($counterparty)
    {
        $counterparty = Counter_parties::find($counterparty);
        $creditors = Financial_transactions::where('counterparty_id', $counterparty->id)->get();
        $data = [];
        foreach ($creditors as $creditor) {
            $data[] = [
                'financial_type' => $creditor->financial_type,
                'counterparty_id' => $creditor->counterparty()->name,
                'amount' => $creditor->amount,
                'case_number' => $creditor->case_number,
                'financial_method' => $creditor->financial_method,
                'invoice_or_cheque_number' => $creditor->invoice_or_cheque_number,
                'transaction_or_cheque_due_date' => $creditor->transaction_or_cheque_due_date,
                'destination_account_name' => $creditor->destination_account_name,
                'destination_account_number' => $creditor->destination_account_number,
                'description' => $creditor->description,

            ];
        }
        return Excel::download(new CounterpartyFinancialTransactionExport(collect($data)), 'گزارش تراکنش های ' . $counterparty->name . '.xlsx');
    }

    public function edit(Financial_transactions $financialTransaction)
    {
        $counterParties = CounterPartyController::getAll();

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
        if ($request->has_destination_account) {
            $validated = $request->validate([
                'amount' => 'required',
                'counterparty_id' => 'required|exists:wf_entity_counter_parties,id',
                'destination_account_id' => 'required|exists:wf_entity_counter_parties,id',
            ], [
                'amount.required' => 'مبلغ الزامی است',
                'counterparty_id.required' => 'طرف حساب الزامی است',
                'destination_account_id.required' => 'طرف حساب مقصد الزامی است',
            ]);
            $destinationCounterparty = DB::table('wf_entity_counter_parties')->where('id', $request->destination_account_id)->first();
        } else {
            $validated = $request->validate([
                'amount' => 'required',
                'counterparty_id' => 'required|exists:wf_entity_counter_parties,id',
            ], [
                'amount.required' => 'مبلغ الزامی است',
                'counterparty_id.required' => 'طرف حساب الزامی است',
            ]);
        }

        if ($request->store_in_pretty_cash) {
            $validated = $request->validate([
                'description' => 'required',
                'amount' => 'required',
                'transaction_or_cheque_due_date' => 'required',
                'counterparty_id' => 'required|exists:wf_entity_counter_parties,id',
            ], [
                'description.required' => 'توضیحات الزامی است',
                'amount.required' => 'مبلغ الزامی است',
                'transaction_or_cheque_due_date.required' => 'تاریخ تراکنش الزامی است',
                'counterparty_id.required' => 'طرف حساب الزامی است',
            ]);
        }

        $counterParty = DB::table('wf_entity_counter_parties')->where('id', $request->counterparty_id)->first();
        $amount = str_replace(',', '', $request->amount);
        $finTransaction = Financial_transactions::create([
            'case_number' => $request->case_number,
            'financial_type' => 'بستانکار',
            'financial_method' => $request->financial_method,
            'description' => $request->description,
            'counterparty_id' => $request->counterparty_id,
            'amount' => (string)$amount,
            'invoice_or_cheque_number' => $request->invoice_or_cheque_number,
            'transaction_or_cheque_due_date' => $request->transaction_or_cheque_due_date,
            'transaction_or_cheque_due_date_alt' => $request->transaction_or_cheque_due_date_alt,
            'destination_account_id' => $request->destination_account_id ?? null,
            'destination_account_name' => $destinationCounterparty->name ?? null,
            'destination_account_number' => $destinationCounterparty->account_number ?? null,
        ]);
        if (isset($destinationCounterparty)) {
            $autoFinTransaction = Financial_transactions::create([
                'case_number' => $request->case_number,
                'financial_type' => 'بدهکار',
                'financial_method' => $request->financial_method,
                'description' => 'تراکنش خودکار. واریزی ' . $counterParty->name,
                'counterparty_id' => $destinationCounterparty->id,
                'amount' => (string)$amount,
                'invoice_or_cheque_number' => $request->invoice_or_cheque_number,
                'transaction_or_cheque_due_date' => $request->transaction_or_cheque_due_date,
                'transaction_or_cheque_due_date_alt' => $request->transaction_or_cheque_due_date_alt,
            ]);
            $finTransaction->auto_financial_transaction_id = $autoFinTransaction->id;
            $finTransaction->save();
        }
        if ($request->store_in_pretty_cash) {
            $data = new Request([
                'title' => $request->description,
                'amount' => (string)$amount,
                'paid_at' => $request->transaction_or_cheque_due_date,
                'from_account' => $counterParty->name,
            ]);
            PettyCashController::store($data);
        }
        return redirect()->back(); //->route('simpleWorkflowReport.financial-transactions.index');
    }

    public function showAddDebit($counterparty = null, $onlyAssignedUsers = false)
    {
        $counterparty = Counter_parties::find($counterparty);
        $counterParties = Counter_parties::when($onlyAssignedUsers, function ($query) {
            $query->whereNotNull('user_id');
        })->get();
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
        return redirect()->back(); //->route('simpleWorkflowReport.financial-transactions.index');
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
        if ($financialTransaction->auto_financial_transaction_id) {
            Financial_transactions::where('id', $financialTransaction->auto_financial_transaction_id)->delete();
        }
        $financialTransaction->delete();

        if ($request->financial_type == 'بستانکار') {
            $this->addCredit($request);
        } else {
            $this->addDebit($request);
        }

        // $financialTransaction->update([
        //     'financial_type' => $validated['financial_type'],
        //     'counterparty_id' => $validated['counterparty_id'],
        //     'case_number' => $validated['case_number'] ?? null,
        //     'amount' => (string) $amount,
        //     'financial_method' => $validated['financial_method'] ?? null,
        //     'invoice_or_cheque_number' => $validated['invoice_or_cheque_number'] ?? null,
        //     'transaction_or_cheque_due_date' => $validated['transaction_or_cheque_due_date'] ?? null,
        //     'transaction_or_cheque_due_date_alt' => $validated['transaction_or_cheque_due_date_alt'] ?? null,
        //     'destination_account_name' => $validated['destination_account_name'] ?? null,
        //     'destination_account_number' => $validated['destination_account_number'] ?? null,
        //     'description' => $validated['description'] ?? null,
        // ]);

        return redirect()
            ->back()
            // ->route('simpleWorkflowReport.financial-transactions.show', $financialTransaction->counterparty_id)
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

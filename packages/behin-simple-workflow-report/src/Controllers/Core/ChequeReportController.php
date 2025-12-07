<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\OnCreditPayment;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;

class ChequeReportController extends Controller
{
    public function index(Request $request)
    {
        $cheques = Financials::whereIn('fix_cost_type', ['تسویه کامل - چک', 'علی الحساب - چک'])
            // ->where('is_passed', null)
            ->get()
            ->groupBy(function ($item) {
                return $item->cheque_number ?: 'unique_' . $item->id;
            });
        $chequeFromOnCredit = OnCreditPayment::where('payment_type', 'چک')->get()->groupBy(function ($item) {
            return $item->cheque_number ?: 'unique_' . $item->id;
        });

        $chequeFromFinancialTransaction = Financial_transactions::where('financial_method', 'چک')
        ->get()
        ->map(function($item){
            $item->cheque_number = $item->invoice_or_cheque_number;
            return $item;
        })
        ->groupBy(function($item){
            return $item->invoice_or_cheque_number ?: 'unique_' . $item->id;
        });

        $counterParties = CounterPartyController::getAll();
        return view('SimpleWorkflowReportView::Core.Cheque.index', compact('cheques', 'chequeFromOnCredit', 'chequeFromFinancialTransaction', 'counterParties'));
    }

    public function updateFromOnCredit(Request $request, $id)
    {

        $cheque = OnCreditPayment::find($id);
        $cheques = OnCreditPayment::where('cheque_number', $cheque->cheque_number)->get();
        foreach ($cheques as $item) {
            if($request->has('cheque_receiver')){
                $item->cheque_receiver = $request->input('cheque_receiver');
            }
            if($request->has('is_passed')){
                $item->is_passed = $request->input('is_passed');
            }
            $item->save();
        }
        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }

    public function update(Request $request, $id)
    {
        if(isset($request->table) and $request->table == 'financial_transaction'){
            $cheque = Financial_transactions::findOrFail($id);
            $cheque->is_passed = 1;
            $cheque->save();
            return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');

        }
        $cheque = Financials::findOrFail($id);

        // اگر کاربر خواسته چک را پاس کند، ولی شماره چک ثبت نشده باشد، ارور بده
        if ($request->has('is_passed') && empty($cheque->cheque_number)) {
            return redirect()->back()->with('error', 'لطفاً ابتدا شماره چک را وارد کنید.');
        }

        if ($request->has('cheque_number')) {
            $cheque->cheque_number = $request->input('cheque_number');
        }

        if ($request->has('cheque_receiver')) {
            $cheque->cheque_receiver = $request->input('cheque_receiver');
        }

        if ($request->has('is_passed')) {
            $cheque->is_passed = true;
            $cheque->payment = $cheque->cost;
            $cheque->payment_date = $cheque->cheque_due_date;
        }

        if ($request->has('destination_account_name')) {
            $counterParty = CounterPartyController::getByName($request->destination_account_name);
            $cheque->destination_account_name = $counterParty->name;
            $cheque->destination_account = $counterParty->account_number;
        }

        $cheque->save();

        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }
}

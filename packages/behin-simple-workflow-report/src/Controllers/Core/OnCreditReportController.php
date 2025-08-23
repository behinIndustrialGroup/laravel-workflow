<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\OnCreditPayment;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class OnCreditReportController extends Controller
{
    public function index(Request $request)
    {
        $onCredits = Financials::whereNotNull('case_number')
            ->whereIn('fix_cost_type', ['حساب دفتری'])
            // ->whereNull('is_passed')
            ->get()->each(function ($row) {
                $row->payments = OnCreditPayment::where('case_number', $row->case_number)->get();
            });
        return view('SimpleWorkflowReportView::Core.OnCredit.index', compact('onCredits'));
    }

    public function edit($id)
    {
        $onCredit = Financials::findOrFail($id);
        $payments = OnCreditPayment::where('case_number', $onCredit->case_number)->get();
        return view('SimpleWorkflowReportView::Core.OnCredit.edit', compact('onCredit', 'payments'));
    }

    public function update(Request $request, $id)
    {
        $onCredit = Financials::findOrFail($id);

        if ($request->has('is_passed')) {
            $onCredit->is_passed = $request->input('is_passed');
            if($onCredit->is_passed == '1'){
                $onCredit->payment = $onCredit->cost;
            }else{
                $onCredit->payment = 0;
            }
            $onCredit->save();
            return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
        }

        $payments = $request->input('payments', []);
        foreach ($payments as $payment) {
            if (!isset($payment['type']) || $payment['type'] === '') {
                continue;
            }

            $fin = new OnCreditPayment();
            $fin->case_number = $onCredit->case_number;
            $fin->case_id = $onCredit->case_id;
            $fin->process_id = $onCredit->process_id;
            $fin->process_name = $onCredit->process_name;
            $fin->payment_type = $payment['type'];

            switch ($payment['type']) {
                case 'نقدی':
                    $fin->amount = isset($payment['cash_amount']) ? str_replace(',', '', $payment['cash_amount']) : null;
                    $fin->date = convertPersianDateToTimestamp($payment['cash_date']);
                    $fin->account_number = $payment['account_number'] ?? null;
                    $fin->account_name = $payment['account_name'] ?? null;
                    $fin->invoice_number = $payment['cash_invoice_number'] ?? null;
                    break;
                case 'چک':
                    $preCheque = OnCreditPayment::where('cheque_number', $payment['cheque_number'])->where('payment_type', 'چک')->first();
                    if($preCheque){
                        if($preCheque->amount != str_replace(',', '',$payment['cheque_amount'])){
                            $response = [
                                'status' => 'error',
                                'message' => 'این شماره چک قبلا با مبلغ ' . number_format($preCheque->amount) . ' برای پرونده ' . $preCheque->case_number . ' ثبت شده است و مبلغ آن با مبلغ وارد شده الان یکسان نیست.'
                            ];
                            break;
                        }
                        if($preCheque->bank_name != $payment['bank_name']){
                            $response = [
                                'status' => 'error',
                                'message' => 'این شماره چک قبلا با نام بانک ' . $preCheque->bank_name . ' برای پرونده ' . $preCheque->case_number . ' ثبت شده است و نام بانک آن با نام بانک وارد شده الان یکسان نیست.'
                            ];
                            break;
                        }
                        if($preCheque->cheque_due_date != convertPersianDateToTimestamp($payment['cheque_due_date']) ){
                            $response = [
                                'status' => 'error',
                                'message' => 'این شماره چک قبلا با سررسید ' . toJalali((int) $preCheque->cheque_due_date)->format('Y-m-d') . ' برای پرونده ' . $preCheque->case_number . ' ثبت شده است و سررسید آن با سررسید وارد شده الان یکسان نیست.'
                            ];
                            break;
                        }
                        if($preCheque->account_name != $payment['account_name']){
                            $response = [
                                'status' => 'error',
                                'message' => 'این شماره چک قبلا با نام حساب مقصد ' . $preCheque->account_name . ' برای پرونده ' . $preCheque->case_number . ' ثبت شده است و نام حساب مقصد آن با نام حساب مقصد وارد شده الان یکسان نیست.'
                            ];
                            break;
                        }
                    }
                    $fin->amount = isset($payment['cheque_amount']) ? str_replace(',', '', $payment['cheque_amount']) : null;
                    $fin->bank_name = $payment['bank_name'] ?? null;
                    $fin->cheque_number = $payment['cheque_number'] ?? null;
                    $fin->cheque_due_date = !empty($payment['cheque_due_date']) ? convertPersianDateToTimestamp($payment['cheque_due_date']) : null;
                    $fin->account_name = $payment['account_name'] ?? null;
                    $fin->invoice_number = $payment['cheque_invoice_number'] ?? null;
                    break;
            }
            if(!isset($response)){
                $fin->save();
            }
        }

        if(isset($response)){
            return response()->json($response);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'با موفقیت ذخیره شد.'
        ]);
    }

    public function showAll(Request $request)
    {
        $onCredits = Financials::whereNotNull('case_number')
            ->whereIn('fix_cost_type', ['حساب دفتری'])
            ->whereNull('is_passed')
            ->get();
        $inboxes = Inbox::whereIn(
            'task_id',
            [
                'adee777f-da9d-4d54-bf00-020a27e0f861',
                'c008cd7d-ea9c-4b0b-917b-97e8ff651155',
                '1c63c629-b27b-4fe6-a993-a7a149926c55'
            ]
        )->where('status', 'new')->get();
        return view('SimpleWorkflowReportView::Core.OnCredit.show-all', compact('onCredits', 'inboxes'));
    }

    public function destroy($id)
    {
        $payment = OnCreditPayment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success', 'با موفقیت حذف شد.');
    }
}

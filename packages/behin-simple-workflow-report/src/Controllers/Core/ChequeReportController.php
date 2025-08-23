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
        return view('SimpleWorkflowReportView::Core.Cheque.index', compact('cheques', 'chequeFromOnCredit'));
    }

    public function updateFromOnCredit(Request $request, $id)
    {

        $cheque = OnCreditPayment::find($id);
        $cheques = OnCreditPayment::where('cheque_number', $cheque->cheque_number)->get();
        foreach ($cheques as $item) {
            $item->cheque_receiver = $request->input('cheque_receiver');
            $item->is_passed = $request->input('is_passed');
            $item->save();
        }
        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }

    public function update(Request $request, $id)
    {
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
        }

        $cheque->save();

        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }
}

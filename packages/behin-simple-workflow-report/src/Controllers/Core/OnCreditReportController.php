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

class OnCreditReportController extends Controller
{
    public function index(Request $request)
    {
        $onCredits = Financials::whereNotNull('case_number')
            ->whereIn('fix_cost_type', ['حساب دفتری'])
            ->whereNull('is_passed')
            ->get();
        return view('SimpleWorkflowReportView::Core.OnCredit.index', compact('onCredits'));
    }

    public function update(Request $request, $id)
    {
        $onCredit = Financials::findOrFail($id);


        if ($request->has('is_passed')) {
            $onCredit->is_passed = true;
        }

        $onCredit->save();

        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }
}

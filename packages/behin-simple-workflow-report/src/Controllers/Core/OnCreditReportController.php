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

    public function showAll(Request $request)
    {
        $onCredits = Financials::whereNotNull('case_number')
            ->whereIn('fix_cost_type', ['حساب دفتری'])
            ->whereNull('is_passed')
            ->get();
        $inboxes = Inbox::whereIn('task_id', 
        ['adee777f-da9d-4d54-bf00-020a27e0f861', 
        'c008cd7d-ea9c-4b0b-917b-97e8ff651155', 
        '1c63c629-b27b-4fe6-a993-a7a149926c55']
        )->where('status', 'new')->get();
        return view('SimpleWorkflowReportView::Core.OnCredit.show-all', compact('onCredits', 'inboxes'));
    }
}

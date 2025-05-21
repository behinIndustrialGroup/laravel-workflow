<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;
use Behin\SimpleWorkflow\Models\Entities\Devices;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class CounterPartyController extends Controller
{
    public function index(Request $request)
    {
        $counterParties = Counter_parties::all();
        return view('SimpleWorkflowReportView::Core.CounterParty.index', compact('counterParties'));
    }

    public function create()
    {
        return view('SimpleWorkflowReportView::Core.CounterParty.create');
    }

    public function store(Request $request)
    {
        $counterParty = Counter_parties::create($request->all());
        return redirect()->route('simpleWorkflowReport.counter-party.index');
    }

    public function show($caseNumber)
    {
        return view(
            'SimpleWorkflowReportView::Core.CounterParty.show',
            compact('mainCase', 'customer', 'devices', 'deviceRepairReports', 'parts', 'financials', 'delivery')
        );
    }

    
}

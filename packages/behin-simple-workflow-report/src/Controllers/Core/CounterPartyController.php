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
use BehinUserRoles\Models\User;
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
        $counterParties = Counter_parties::with('user')->orderBy('name')->get();
        return view('SimpleWorkflowReportView::Core.CounterParty.index', compact('counterParties'));
    }


    public static function getAll(){
        return Counter_parties::all();
    }

    public static function getByName($name){
        return Counter_parties::where('name', $name)->first();
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('SimpleWorkflowReportView::Core.CounterParty.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'state' => ['nullable', 'string', 'max:255'],
        ]);

        Counter_parties::create($validated);
        return redirect()->route('simpleWorkflowReport.counter-party.index');
    }

    public function show($caseNumber)
    {
        return view(
            'SimpleWorkflowReportView::Core.CounterParty.show',
            compact('mainCase', 'customer', 'devices', 'deviceRepairReports', 'parts', 'financials', 'delivery')
        );
    }

    public function destroy($id)
    {
        Counter_parties::destroy($id);
        return redirect()->route('simpleWorkflowReport.counter-party.index');
    }

    
}

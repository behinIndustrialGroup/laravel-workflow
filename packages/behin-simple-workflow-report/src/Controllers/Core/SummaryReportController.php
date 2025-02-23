<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryReportController extends Controller
{
    public function index(): View
    {
        $processes = getProcesses();
        return view('SimpleWorkflowReportView::Core.Summary.index', compact('processes'));
    }

    public function show($process_id)
    {
        $process= ProcessController::getById($process_id);
        $view = 'SimpleWorkflowReportView::Core.Summary.process.' . $process_id;
        if(view()->exists($view)){
            return view($view, compact('process'));
        }
        return view('SimpleWorkflowReportView::Core.Summary.show', compact('process'));
    }

    public function edit($caseId) {
        $case = CaseController::getById($caseId);
        $process = $case->process;
        $summaryForm = RoleReportFormController::getSummaryReportFormByRoleId(Auth::user()->role_id, $process->id);
        if($summaryForm == null){
            return redirect()->back()->with('error', trans('Form not found'));  
        }
        $formId = $summaryForm->summary_form_id;

        $form = FormController::getById($formId);

        return view('SimpleWorkflowReportView::Core.Summary.edit', compact('case','form','process'));
    }
}

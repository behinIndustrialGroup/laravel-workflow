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

class ReportController extends Controller
{
    public function index(): View
    {
        $processes = getProcesses();
        return view('SimpleWorkflowReportView::Core.Report.index', compact('processes'));
    }

    public function show($process_id)
    {
        $process= ProcessController::getById($process_id);
        return view('SimpleWorkflowReportView::Core.Report.show', compact('process'));
    }

    public function edit($caseId) {
        $case = CaseController::getById($caseId);
        $process = $case->process;
        $form = FormController::getById($process->report_form_id);

        return view('SimpleWorkflowReportView::Core.Report.edit', compact('case','form','process'));
    }
}

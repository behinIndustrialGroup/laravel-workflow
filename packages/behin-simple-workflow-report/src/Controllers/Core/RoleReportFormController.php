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
use BehinUserRoles\Controllers\GetRoleController;
use BehinUserRoles\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleReportFormController extends Controller
{
    public function index(): View
    {
        $roles = GetRoleController::getAll();
        return view('SimpleWorkflowReportView::Core.Role.index', compact('roles'));
    }

    public function show($process_id)
    {
        $process= ProcessController::getById($process_id);
        return view('SimpleWorkflowReportView::Core.Summary.show', compact('process'));
    }

    public function edit($caseId) {
        $case = CaseController::getById($caseId);
        $process = $case->process;
        if(Auth::user()->role_id == 1){
            $formId = "";
        }elseif(Auth::user()->role_id == 2){
            $formId = "";
        }elseif(Auth::user()->role_id == 3){
            $formId = "";
        }else{
            $formId = "";
        }
        $form = FormController::getById($formId);

        return view('SimpleWorkflowReportView::Core.Report.edit', compact('case','form','process'));
    }

    public static function update(Request $request, Role $role){
        $role->summary_report_form_id = $request->summary_report_form_id;
        $role->save();
        return redirect()->back();
    }

}

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflowReport\Models\Core\RoleForm;
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
        $role_forms = self::getAll();
        return view('SimpleWorkflowReportView::Core.Role.index', compact('roles', 'role_forms'));
    }

    public static function getAll(){
        return RoleForm::get();
    }

    public function show($process_id)
    {
        $process= ProcessController::getById($process_id);
        return view('SimpleWorkflowReportView::Core.Summary.show', compact('process'));
    }

    public static function update(Request $request, Role $role){
        $role->summary_form_id = $request->summary_form_id;
        $role->save();
        return redirect()->back();
    }

    public static function store(Request $request){
        $row = RoleForm::create([
            'role_id' => $request->role_id,
            'summary_form_id' => $request->summary_form_id,
            'process_id' => $request->process_id
        ]);
        return redirect()->back();
    }

    public static function getSummaryReportFormByRoleId($role_id){
        return RoleForm::where('role_id', $role_id)->first();
    }

}

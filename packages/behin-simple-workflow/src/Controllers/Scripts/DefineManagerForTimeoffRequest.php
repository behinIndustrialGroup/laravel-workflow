<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;


class DefineManagerForTimeoffRequest extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $userId = $this->case->creator;//Auth::id();
        $user = User::find($userId);
        $userDepartments = $user->departments()->pluck('department_id')->toArray();
        
        
        $departments = [5,6,7];
        $intersect = array_intersect($userDepartments, $departments);
        $intersect = array_values($intersect);
        
        $userDepartment = isset($intersect[0]) ? $intersect[0] : false;
        if(!$userDepartment){
            return "دپارتمان کاربر تعریف نشده است";
        }
        $departmentManager = DepartmentController::get($userDepartment)?->manager;
        if($userId == $departmentManager){
            $departmentManager = 43;
        }
        VariableController::save(
            $this->case->process->id,     
            $this->case->id,
            'department_manager',
            $departmentManager
        );
        VariableController::save(
            $this->case->process->id,     
            $this->case->id,
            'creator_name',
            $user->name
        );
        
    }
}
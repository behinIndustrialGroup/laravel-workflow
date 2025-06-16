<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BehinUserRoles\Models\UserDepartment;
use BehinUserRoles\Models\User;

class ChangeMapaExpertAccordingToUnit extends Controller
{
    protected $case;
    public function __construct($case = null)
    {
        // $this->case = $case;
    }

    public function execute(Request $request)
    {
        if($request->unit == 'Drive'){
            $users = UserDepartment::where('department_id', 1)->pluck('user_id');
        }
        if($request->unit == 'کنترل'){
            $users = UserDepartment::where('department_id', 2)->pluck('user_id');
        }
        if($request->unit == 'سیم پیچ'){
            $users = UserDepartment::where('department_id', 3)->pluck('user_id');
        }
        if($request->unit == 'مانیتور'){
            $users = UserDepartment::where('department_id', 4)->pluck('user_id');
        }
        
        $users = User::whereIn('id', $users)->select('name as label', 'id as value')->get();
        return $users;
    }
}
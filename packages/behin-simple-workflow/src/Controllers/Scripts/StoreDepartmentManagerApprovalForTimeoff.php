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
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;



class StoreDepartmentManagerApprovalForTimeoff extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $variables = $this->case->variables();
        
        $uniqueId = $variables->where('key', 'timeoff_uniqueId')->first()?->value;
        $description = $case->getVariable('user_department_manager_description');
        $approved = $case->getVariable('user_department_manager_approval');
        $approvedBy = $case->getVariable('timeoff_approval_by');
        if($approved == 'تایید است'){
            $approved = 1;
        }else{
            $approved = 0;
        }
        if(!$uniqueId){
            $uniqueId = $case->id;
        }
        Timeoffs::updateOrCreate([
            'uniqueId' => $uniqueId
            ],
            [
            'approved' => $approved,
            'approved_by' => $approvedBy,
            'description' => $description
        ]);
        
    }
}
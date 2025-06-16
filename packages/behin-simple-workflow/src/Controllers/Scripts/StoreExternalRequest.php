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
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities;



class StoreExternalRequest extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public static function execute(Request $request = null)
    {
        Entities\Customers::create([
            'customer_workshop_or_ceo_name' => ,
            'mobile' => ,
            'national_id' => ,
            'province' => ,
            'address' => 
        ]);
        Entities\Repair_requests::create([
            'number' => ,
            'creation_date' => ,
            'customer_initial_description' => ,
            'mapa_expert_head' => ,
        ]);
        
        
    }
}
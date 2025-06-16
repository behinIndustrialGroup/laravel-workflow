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



class StoreMapaExpert extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $partId = $case->getVariable('part_id');
        $part = Entities\Parts::find($partId);
        
        $part->mapa_expert = $case->getVariable('mapa_expert');
        $part->save();
        
        $case->saveVariable('part_name', $part->name);
        $case->saveVariable('mapa_expert_head', $part->mapa_expert_head);
        $case->saveVariable('refer_to_unit', $part->refer_to_unit);
        
        // $part = Entities\Parts::updateOrCreate(
        //     [
        //         'id'=> $case->getVariable('part_id'),
        //         'case_number' => $case->number,
        //         'device_id' => $case->getVariable('device_id'),   
        //     ],
        //     [
        //         'refer_to_unit' => $case->getVariable('refer_to_unit'), 
        //         'mapa_expert' => $case->getVariable('mapa_expert'),   
        //     ]
        // );
    }
}
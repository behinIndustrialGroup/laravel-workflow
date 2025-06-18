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
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use BehinFileControl\Controllers\FileController;



class AddPartToCase extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = $case;   
    }

    public function execute()
    {
        // $case = $this->case;
        // $case->number = $case->getVariable('case_number');
        // $case->save();
        // Parts::create([
        //     'case_id' => $case->id,
        //     'case_number' => $case->getVariable('case_number'),
        //     'name' => $case->getVariable('part_name'),
        //     'mapa_serial' => $case->getVariable('mapa_serial'),
        //     'mapa_expert_head' => $case->getVariable('mapa_expert_head_for_internal_process'),
        //     'refer_to_unit' => $case->getVariable('refer_to_unit'),
        //     'initial_part_pic' => $case->getVariable('initial_part_pic'),
        //     'has_attachment' => $case->getVariable('has_attachment'),
        //     'attachment_image' => $case->getVariable('attachment_image'),
        //     ]);
            
        // $newCase = new StartInternalRepairForEachPart($case);
        // $newCase->execute();
    }
}
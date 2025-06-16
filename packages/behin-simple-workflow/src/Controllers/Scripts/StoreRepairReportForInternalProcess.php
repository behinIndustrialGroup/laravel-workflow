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
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreRepairReportForInternalProcess extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $caseNumber = $this->case->number;
        $variable = $this->case->variables();
        // Repair_reports::create(
        //     [
        //         'case_id' => $this->case->id,
        //         'case_number' => $caseNumber,
        //         'creator' => getUserInfo($variable->where('key', 'mapa_expert')->first()->value)->name ?? '',
        //         'report' => $variable->where('key','fix_report')->first()->value,
        //         'duration' => $variable->where('key','repair_duration')->first()?->value,
        //         'mapa_expert' => getUserInfo($variable->where('key', 'mapa_expert')->first()->value)->name,
        //         'mapa_expert_head' => getUserInfo($variable->where('key', 'mapa_expert_head_for_internal_process')->first()->value)->name,
        //     ]
        //     );
    }
}
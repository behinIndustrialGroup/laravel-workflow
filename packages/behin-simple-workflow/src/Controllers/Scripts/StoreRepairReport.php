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

class StoreRepairReport extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
        //
        // VariableController::save(
        //     $newInbox->case->process_id, $newInbox->case->id, 'customer_name', $case->variables()->where('name', 'customer_name')->first()->value
        // );
        // VariableController::save(
        //     $newInbox->case->process_id, $newInbox->case->id, 'customer_nid', $case->variables()->where('name', 'customer_nid')->first()->value
        // );
        // VariableController::save(
        //     $newInbox->case->process_id, $newInbox->case->id, 'customer_mobile', $case->variables()->where('name', 'customer_mobile')->first()->value
        // );
        // $this->case = $case;
        // return VariableController::save(
        //     $this->case->process_id, $this->case->id, 'manager', 2
        // );
    }

    public function execute()
    {
        $caseNumber = $this->case->number;
        $variable = $this->case->variables();
        Repair_reports::create(
            [
                'case_number' => $caseNumber,
                'creator' => getUserInfo($variable->where('key', 'mapa_expert')->first()->value)->name ?? '',
                'report' => $variable->where('key','fix_report')->first()->value ?? '',
                'start_date' => $variable->where('key','fix_start_date')->first()->value?? '',
                'start_time' => $variable->where('key','fix_start_time')->first()->value?? '',
                'end_date' => $variable->where('key','fix_end_date')->first()->value?? '',
                'end_time' => $variable->where('key','fix_end_time')->first()->value?? '',
            ]
            );
    }
}

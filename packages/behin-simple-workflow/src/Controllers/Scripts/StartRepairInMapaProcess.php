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

class StartRepairInMapaProcess extends Controller
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
        Log::info($this->case->variables()->where('key', 'customer_name'));
        $task = TaskController::getById("9f6b7b5c-155e-4698-8b05-26ebb061bb7d");
        $newInbox = ProcessController::start($task->id, true, false);
        $parent_case_number = $this->case->number;
        $newCaseId = $newInbox->case->id;
        $newCaseNumber = $newInbox->case->number;
        $mapa_expert = $this->case->variables()->where('key', 'mapa_expert')->first()->value ?? '';
        $mapa_expert_name = getUserInfo($mapa_expert)?->name;
        $customer_workshop_or_ceo_name = $this->case->variables()->where('key', 'customer_workshop_or_ceo_name')->first()->value ?? '';
        VariableController::save(
            $newInbox->case->process_id,
            $newInbox->case->id,
            'customer_workshop_or_ceo_name',
            $customer_workshop_or_ceo_name
        );
        VariableController::save(
            $newInbox->case->process_id,
            $newInbox->case->id,
            'customer_nid',
            $this->case->variables()->where('key', 'customer_nid')->first()->value ?? ''
        );
        VariableController::save(
            $newInbox->case->process_id,
            $newInbox->case->id,
            'customer_mobile',
            $this->case->variables()->where('key', 'customer_mobile')->first()->value ?? ''
        );
        $initial_description = "ارجاع شده از فرایند تعمیر در محل به شماره پرونده " . $parent_case_number;
        $initial_description .= "این پرونده توسط " . $mapa_expert_name . "انجام شده است ";
        VariableController::save(
            $newInbox->case->process_id,
            $newInbox->case->id,
            'initial_description',
            $initial_description
        );
        VariableController::save(
            $newInbox->case->process_id,
            $newInbox->case->id,
            'parent_case_numner',
            $parent_case_number
        );
        VariableController::save(
            $newInbox->case->process_id,
            $newInbox->case->id,
            'parent_case_id',
            $this->case->id
        );
        // Log::info('newInbox');
        // Log::info($newInbox);
        // Log::info('end newInbox');
        // Log::info('start variables');
        // Log::info($case->variables());
        // Log::info('end variables');
    }
}

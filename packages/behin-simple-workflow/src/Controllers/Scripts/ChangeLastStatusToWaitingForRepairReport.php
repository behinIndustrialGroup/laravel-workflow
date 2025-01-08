<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;

class ChangeLastStatusToWaitingForRepairReport extends Controller
{
    protected $case;
    public function __construct($case) {
        $this->case = $case;
        
    }

    public function execute()
    {
        // $last_status = $this->case->variables()->where('key', 'last_status')->frist()->value;
        VariableController::save(
            $this->case->process_id, $this->case->id, 'last_status', 'در انتظار انجام تعمیرات'
        );
    }

}

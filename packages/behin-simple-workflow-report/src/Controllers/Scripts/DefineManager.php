<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;

class DefineManager extends Controller
{
    protected $case;
    public function __construct($case) {
        $this->case = $case;
        return VariableController::save(
            $this->case->process_id, $this->case->id, 'manager', 2
        );
    }

    public function execute()
    {


    }

}

<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;

class CreateMapaCenterVariable extends Controller
{
    protected $case;
    public function __construct($case) {
        $this->case = $case;
    }

    public function execute()
    {
        $case = $this->case;
        if(!$case->getVariable('from_mapa_center')){
            $case->saveVariable('from_mapa_center', 'no');
        }
    }

}
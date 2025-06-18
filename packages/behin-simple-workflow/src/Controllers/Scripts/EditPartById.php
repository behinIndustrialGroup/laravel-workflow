<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Entities\Parts;



class EditPartById extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = $case;   
    }

    public function execute()
    {
        $case = $this->case;
        $part = Parts::find($case->getVariable('part_id'));
        $part->name = $case->getVariable('part_name');
        $part->save();
    }
}
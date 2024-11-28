<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VariableController extends Controller
{
    public static function getVariablesByCaseId($caseId)
    {
        return Variable::where('case_id', $caseId)->get();
    }

    public static function save($processId, $caseId, $key, $value)
    {
        Variable::updateOrCreate(
            [
                'process_id' => $processId,
                'case_id' => $caseId,
                'key' => $key
            ],
            [
                'value' => $value
            ]
        );
    }

}

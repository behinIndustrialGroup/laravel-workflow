<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use BehinFileControl\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VariableController extends Controller
{
    public static function getVariablesByCaseId($caseId)
    {
        return Variable::where('case_id', $caseId)->get();
    }

    public static function getVariable($processId, $caseId, $key)
    {
        return Variable::where('process_id', $processId)->where('case_id', $caseId)->where('key', $key)->first();
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

    public static function saveFile($processId, $caseId, $key, $value)
    {
        // $row = self::getVariable($processId, $caseId, $key);
        // $paths = [];
        // if ($row) {
        //     $paths = json_decode($row->value);

        // }
        $result = FileController::store($value, 'simpleWorkflow');
            if ($result['status'] == 200) {
                Variable::create([
                    'process_id' => $processId,
                    'case_id' => $caseId,
                    'key' => $key,
                    'value' => $result['dir']
                ]);
            }
    }
}

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
use Illuminate\Support\Facades\DB;

class VariableController extends Controller
{
    public static function getVariablesByCaseId($caseId, $processId = null)
    {
        if ($processId) {
            return Variable::where('case_id', $caseId)->where('process_id', $processId)->get();
        }
        return Variable::where('case_id', $caseId)->get();
    }

    public static function getVariable($processId, $caseId, $key)
    {
        $variable = Variable::where('process_id', $processId)->where('case_id', $caseId)->where('key', $key)->first();
        return $variable;
    }

    public static function getAll(array $fields = ['customer_fullname', 'customer_mobile', 'repair_cost'])
    {
        $rows = [];
        foreach ($fields as $field) {
            $rows[] = DB::raw("MAX(CASE WHEN `key` = '$field' THEN value END) as $field");
        }

        $searchQuery = DB::table('wf_variables')
            ->select(
                'case_id',
                'process_id',
                ...$rows
            )
            ->groupBy('case_id')
            ->get();
        return $searchQuery;
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

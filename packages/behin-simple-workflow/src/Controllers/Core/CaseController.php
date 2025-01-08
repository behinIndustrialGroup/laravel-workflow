<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CaseController extends Controller
{
    public static function getById($id): Cases {
        return Cases::find($id);
    }

    public static function create($processId, $creator, $name = null)
    {
        $lastNumber = Cases::where('process_id', $processId)->orderBy('number', 'desc')->first()?->number;
        $lastNumber = (int) $lastNumber;
        $newNumber = $lastNumber ? $lastNumber + 1 : config('workflow.caseStartValue');
        Log::info($newNumber);
        return Cases::create([
            'process_id' => $processId,
            'number' => $newNumber,
            'name' => $name,
            'creator' => $creator
        ]);
    }

    public static function getProcessCases($processId)
    {
        return Cases::where('process_id', $processId)->get();
    }

}

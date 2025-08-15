<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\CaseNumbering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CaseController extends Controller
{
    public static function getById($id) {
        return Cases::find($id);
    }

    public static function create($processId, $creator, $name = null, $inDraft = false, $caseNumber = null, $parentId = null)
    {
        if($inDraft) {
            return Cases::create([
                'process_id' => $processId,
                'number' => null,
                'name' => $name,
                'creator' => $creator,
                'parent_id' => $parentId
            ]);
        }
        $newNumber = $caseNumber? $caseNumber : self::getNewCaseNumber($processId);
        return Cases::create([
            'process_id' => $processId,
            'number' => $newNumber,
            'name' => $name,
            'creator' => $creator,
            'parent_id' => $parentId
        ]);
    }

    public static function getNewCaseNumber($processId){
        $process = Process::findOrFail($processId);
        if($process->case_prefix){
            $start = config('workflow.caseStartValue', 1);
            $numbering = CaseNumbering::firstOrCreate(
                ['prefix' => $process->case_prefix],
                ['count' => $start - 1]
            );
            $numbering->count = $numbering->count + 1;
            $numbering->save();

            return $process->case_prefix . '-' . str_pad($numbering->count, 4, '0', STR_PAD_LEFT);
        }

        if (config('workflow.caseNumberingPerCategory')) {
            $category = ProcessController::getById($processId)?->category;
            $processIds = Process::where('category', $category)->pluck('id');
            $lastNumber = Cases::whereIn('process_id', $processIds)->orderByRaw('CAST(number AS UNSIGNED) DESC')->first()?->number;
        }
        elseif(config('workflow.caseNumberingPerProcess')){
            $lastNumber = Cases::where('process_id', $processId)->orderByRaw('CAST(number AS UNSIGNED) DESC')->first()?->number;
        }else{
            $lastNumber = Cases::orderByRaw('CAST(number AS UNSIGNED) DESC')->first()?->number;
        }
        $newNumber = $lastNumber ? $lastNumber + 1 : config('workflow.caseStartValue');
        return $newNumber;
    }

    public static function setCaseNumber($caseId, $number){
        Cases::where('id', $caseId)->update(['number' => $number]);
    }

    public static function getProcessCases($processId)
    {
        return Cases::where('process_id', $processId)->get();
    }

    public static function getAll()
    {
        return Cases::all();
    }

    public static function getAllByCaseNumber($caseNumber){
        return Cases::where('number', $caseNumber)->get();
    }

}

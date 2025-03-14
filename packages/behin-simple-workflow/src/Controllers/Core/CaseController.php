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
        if(config('workflow.caseNumberingPerProcess')){
            $lastNumber = Cases::where('process_id', $processId)->orderBy('number', 'desc')->first()?->number;
        }else{
            $lastNumber = Cases::orderBy('number', 'desc')->first()?->number;
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

}

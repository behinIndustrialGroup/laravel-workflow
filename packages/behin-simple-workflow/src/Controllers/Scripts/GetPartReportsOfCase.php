<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Illuminate\Http\Request;

class GetPartReportsOfCase extends Controller
{
    private $case;

    public function __construct()
    {
        // $this->case = CaseController::getById($case->id);
    }

    public static function execute(Request $request)
    {
        if($request->caseId and $request->caseId != 'undefined'){
            $caseId = $request->caseId;
            $case = CaseController::getById($caseId);
            return $partReports = Part_reports::where('case_number', $case->number)->get()->each(function($row){
                $row->part = $row->part();
                $row->part->mapa_expert_head = $row->part()->mapaExpertHead();
                $row->part->mapa_expert = $row->part()->mapaExpert();
            });
        }
    }
}
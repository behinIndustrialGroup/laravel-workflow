<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Devices;

class StoreExternalJobRank extends Controller
{
    private $case;

    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        $caseNumber = $case->number;
        $report = Repair_reports::where('case_number', $caseNumber)->whereNull('repair_is_approved')->update([
                'job_rank' => $case->getVariable('job_rank')
            ]);
    }
}
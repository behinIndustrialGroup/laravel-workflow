<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Illuminate\Http\Request;

class SetPartHead extends Controller
{
    private $case;

    public function __construct($case)
    {
        $this->case = $case;
    }

    public static function execute(Request $request)
    {
        
    }
}
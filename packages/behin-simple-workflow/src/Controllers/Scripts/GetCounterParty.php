<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;
use Illuminate\Http\Request;

class GetCounterParty extends Controller
{
    private $case;

    public function __construct()
    {
        // $this->case = CaseController::getById($case->id);
    }

    public static function execute(Request $request)
    {
        if($request->q){
            return Counter_parties::where('name', 'like', "%$request->q%")->get();
        }
    }
}
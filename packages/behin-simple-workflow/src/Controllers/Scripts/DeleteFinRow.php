<?php
namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Morilog\Jalali\Jalalian;

class DeleteFinRow extends Controller
{
    public function execute(Request $request = null)
    {
        $caseId = $request->caseId;
        $finId = $request->finId;
        $financial = Financials::find($finId);
        if(!$financial){
            return "ردیف مالی وجود ندارد";
        }
        
        // if($financial->case_id != $caseId){
        //     return "امکان حذف رکورد مالی وجود ندارد";
        // }
        
        $financial->delete();
    }
}
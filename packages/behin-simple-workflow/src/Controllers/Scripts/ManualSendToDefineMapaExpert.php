<?php
namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\RoutingController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Morilog\Jalali\Jalalian;

class ManualSendToDefineMapaExpert extends Controller
{
    public function execute(Request $request = null)
    {
        $caseId = $request->caseId;
        $inboxId = $request->inboxId;
        $inbox = InboxController::getById($inboxId);
        $case = CaseController::getById($caseId);
        $children = $case->children();
        $taskId = "7b96d0c0-e2aa-43d2-bcda-67bcfb4b8c87";
        foreach($children as $childCase){
            $r = new Request([
                'inboxId' => $inboxId,
                'caseId' => $childCase->id,
                'processId' => $childCase->process_id,
                'taskId' => $inbox->task->id,
                'next_task_id' => $taskId
                ]);
            RoutingController::jumpTo($r);
        }
        
        $inbox->status = 'done';
        $inbox->save();
    }
}
<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController as CoreProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

use Behin\SimpleWorkflowReport\Controllers\Scripts\TimeoffExport;
use Behin\SimpleWorkflowReport\Controllers\Scripts\TimeoffExport2;
use Maatwebsite\Excel\Facades\Excel;


class ProcessController extends Controller
{
    public function update(Request $request, $processId)
    {
        $process = CoreProcessController::getById($processId);
        $year = Jalalian::now()->format('%Y');
        DB::table('wf_entity_timeoffs')->where('user', $request->userId)->where('request_year', $year)->update(['deleted_at' => Carbon::now()]);;
        $duration = $request->restBySystem - $request->restByUser;
        DB::table('wf_entity_timeoffs')->insert(
            [
                'user' => $request->userId,
                'type' => 'ساعتی',
                'duration' => $duration,
                'approved' => 1,
                'request_year' => $year,
                'request_month' => Jalalian::now()->format('%m'),
                'uniqueId' => 'به صورت دستی'
            ]
        );
        return redirect()->back();
    }

    public function export($processId){
        $process = CoreProcessController::getById($processId);
        return Excel::download(new TimeoffExport, 'timeoff_report.xlsx');
    }

    public function export2($processId, $userId = null){
        $process = CoreProcessController::getById($processId);
        return Excel::download(new TimeoffExport2($userId), 'timeoff_report2.xlsx');
    }
}

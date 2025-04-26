<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;


class ExpiredController extends Controller
{
    public static function index(){
        $now = Carbon::now();
        $expiredTasks = Inbox::whereHas('task', function ($query) use ($now) {
            $query->whereNotNull('duration');
        })
        ->whereRaw('UNIX_TIMESTAMP(created_at) + (SELECT duration FROM wf_task WHERE wf_task.id = wf_inbox.task_id) * 60 < ?', [$now->timestamp])
        ->where('status', 'new')
        ->get();
        return view('SimpleWorkflowReportView::Core.Summary.process.partial.expired-tasks', compact('expiredTasks'));
    }
}

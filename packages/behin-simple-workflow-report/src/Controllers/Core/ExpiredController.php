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
        $tasks = Task::whereNotNull('duration')->get();
        $expiredTasks = Inbox::whereIn('task_id', $tasks->pluck('id'))->where('status', 'new')->with('task', 'actor')->get();
        return view('SimpleWorkflowReportView::Core.Summary.process.partial.expired-tasks', compact('expiredTasks'));
    }
}

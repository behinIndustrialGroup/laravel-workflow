<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class PersonelActivityController extends Controller
{
    public function index(Request $request)
    {
        $users = User::get()->each(function ($row) {
            $row->inbox = Inbox::where('actor', $row->id)->where('status', 'new')->count();
            $row->done = Inbox::where('actor', $row->id)->where('status', 'done')->count();
        });
        return view('SimpleWorkflowReportView::Core.PersonelActivity.index', compact('users'));
    }

    public function showDones($user_id)
    {
        $allowedProcessIds = ['35a5c023-5e85-409e-8ba4-a8c00291561c',
        '4bb6287b-9ddc-4737-9573-72071654b9de',
    'ee209b0a-251c-438e-ab14-2018335eba6d'];

        $items = Inbox::where('actor', $user_id)
            ->where('status', 'done')
            ->whereHas('task.process', function ($query) use ($allowedProcessIds) {
                $query->whereIn('id', $allowedProcessIds);
            })
            ->with(['case', 'task.process']) // eager load برای جلوگیری از n+1
            ->get()
            ->unique(function ($item) {
                return $item->case_id;
            });

        return view('SimpleWorkflowReportView::Core.PersonelActivity.show', compact('items'));
    }
}

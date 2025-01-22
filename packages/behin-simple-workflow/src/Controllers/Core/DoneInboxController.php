<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use BehinProcessMaker\Controllers\ToDoCaseController;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DoneInboxController extends Controller
{
    public function index(): View
    {
        $rows = self::getUserInbox(Auth::id());
        return view('SimpleWorkflowView::Core.DoneInbox.list')->with([
            'rows' => $rows
        ]);
    }

    public static function getUserInbox($userId): Collection
    {
        $rows = Inbox::where('actor', $userId)->whereIn('status', ['done', 'doneByOther'])->with('task')->orderBy('created_at', 'desc')->get();
        return $rows;
    }
}

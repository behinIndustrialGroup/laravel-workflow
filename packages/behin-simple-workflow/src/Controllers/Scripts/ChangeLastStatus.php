<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeLastStatus extends Controller
{
    protected $case;
    public function __construct($case)
    {
        $this->case = $case;
    }

    public function execute()
    {
        $today = Carbon::today();

        $records = DB::table('wf_cases')
            ->select('process_id', DB::raw('COUNT(*) as record_count'))
            ->whereDate('created_at', $today)
            ->groupBy('process_id')
            ->first();
        return $records->record_count;
    }
}

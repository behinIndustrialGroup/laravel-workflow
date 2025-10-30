<?php

namespace Behin\SimpleWorkflow\Jobs;

use Behin\SimpleWorkflow\Controllers\Core\RoutingController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteNextTaskWithDelay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $caseId;

    public function __construct($task, $caseId)
    {
        $this->task = $task;
        $this->caseId = $caseId;
    }

    public function handle()
    {
        Log::info('Executing next task with delay for task: ' . $this->task->id);
        Log::info('Executing next task with delay for case: ' . $this->caseId);
        if ($this->task) {
            Log::info('done');
            $executedTaskIds = [];
            RoutingController::executeNextTask($this->task, $this->caseId, $executedTaskIds);
        }
    }
}


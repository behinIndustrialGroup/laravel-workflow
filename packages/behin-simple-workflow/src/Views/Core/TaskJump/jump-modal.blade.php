<div class="">
    <h2 class="text-center">{{ trans('fields.Jump To Task') }}</h2>
    @foreach ($task->jumps as $jump)
        <form action="{{ route('simpleWorkflow.routing.jumpTo') }}" class="m-1" method="POST">
            @csrf
            <input type="hidden" name="inboxId" id="inboxId" value="{{ $inbox_id }}">
            <input type="hidden" name="caseId" id="caseId" value="{{ $case_id }}">
            <input type="hidden" name="taskId" id="taskId" value="{{ $task->id }}">
            <input type="hidden" name="processId" id="processId" value="{{ $process_id }}">
            <input type="hidden" name="next_task_id" id="" value="{{ $jump->next_task_id }}">
            <button class="btn btn-default col-sm-12">{{ $jump->nextTask->name }}</button>
        </form>
    @endforeach
</div>

<div class="p-2">
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h2>{{ trans('fields.Edit Task Jump') }}</h2>
    @foreach ($task->jumps as $jump)
        <div class="row card p-3">
            <div>
                <form action="{{ route('simpleWorkflow.task-jump.update', $jump->id) }}" method="POST"
                    class="row col-sm-12">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                    <input type="text" name="" id="" class="form-control col-sm-1"
                        value="{{ $loop->iteration }}" readonly>
                    <div class="input-group col-sm-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ trans('fields.Name') }}</span>
                        </div>
                        <input type="text" class="form-control" value="{{ $jump->nextTask->name }}" readonly>
                    </div>
                    <div class="input-group col-sm-5">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ trans('fields.Next Task') }}</span>
                        </div>
                        <input type="text" name="next_task_id" value="{{ $jump->next_task_id }}" list="tasks"
                            class="form-control">
                        <datalist id="tasks">
                            @foreach ($task->process->tasks() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="input-group col-sm-2">
                        <button type="submit" class="btn btn-primary">{{ trans('fields.Update') }}</button>
                        <button type="submit" name="delete" value="1"
                            class="btn btn-danger">{{ trans('fields.Delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    <form action="{{ route('simpleWorkflow.task-jump.store') }}" method="POST">
        @csrf
        <input type="hidden" name="task_id" value="{{ $task->id }}">
        <div class="row col-sm-12">
            <div class="row col-sm-6">
                <div class="input-group col-sm-6">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ trans('fields.Name') }}</span>
                    </div>
                    <input type="text" class="form-control" id="name" name="next_task_id" list="tasks"
                        aria-label="{{ trans('fields.Name') }}">
                    <datalist id="tasks">
                        @foreach ($task->process->tasks() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-sm-1">
                    <button type="submit" class="btn btn-primary">{{ trans('fields.Add') }}</button>
                </div>
            </div>

        </div>
    </form>
</div>

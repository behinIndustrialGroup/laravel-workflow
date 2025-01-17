@extends('behin-layouts.app')

@section('title', trans('Edit Process'))

@section('content')
    <div class="container">

        <h2>{{ $process->name }}</h2>
        <button onclick="check_error()" class="btn btn-danger">
            {{ trans('fields.Check Error') }}
        </button>
        @foreach ($process->startTasks() as $task)
            @php
                $bgColor =
                    $task->type == 'form' ? 'bg-primary' : ($task->type == 'script' ? 'bg-success' : 'bg-warning');
            @endphp
            <div class="">
                @csrf
                <div class="p-2 bg-light">
                    <a type="submit" class="" style=""
                    href="{{ route('simpleWorkflow.task.edit', $task->id) }}"><i class="fa fa-edit"></i></a>
                    <strong class="">
                        <a data-toggle="collapse" href="#{{ $task->id }}">{{ $task->name }}</a>
                        <span class="badge {{ $bgColor }}">
                            {{ ucfirst($task->type) }}
                        </span>
                        {{-- <input type="hidden" name="id" value="{{ $task->id }}"> --}}
                        {{-- <div class="flex-grow-1" style="display: block">
                            <span class="badge {{ $bgColor }}">{{ trans('Executive File') }} :
                                {{ $task->executive_element_id ? $task->executiveElement()->name : '' }}
                            </span>
                            @if ($task->assignment_type)
                                <span class="badge {{ $bgColor }}">{{ trans('Assignment') }}:
                                    {{ $task->assignment_type }}
                                </span>
                            @endif
                            @if ($task->actors()->count() > 0)
                                <span class="badge bg-info">{{ trans('Actors') }}:
                                    {{ $task->actors()->pluck('actor')->implode(', ') }}
                                </span>
                            @endif
                            @if ($task->next_element_id)
                                @php
                                    $bgColor =
                                        $task->nextTask()->type == 'form'
                                            ? 'bg-primary'
                                            : ($task->nextTask()->type == 'script'
                                                ? 'bg-success'
                                                : 'bg-warning');
                                @endphp
                                <span class="badge {{ $bgColor }}">{{ trans('Next Task') }} :
                                    {{ $task->nextTask()->name }}
                                </span>
                            @endif
                        </div> --}}
                    </strong>
                    @if ($error = taskHasError($task->id))
                        <i class="fa fa-exclamation-triangle text-danger" title="{{ $error['descriptions'] }}"></i>
                    @endif


                </div>
                <div id="{{ $task->id }}" class="">
                    {{-- <div class="panel-body"> --}}
                        @php
                            $children = $task->children();
                        @endphp
                        @if (count($children))
                            @include('SimpleWorkflowView::Core.Task.tree', [
                                'children' => $children,
                                'level' => 1,
                            ])
                        @endif
                    {{-- </div> --}}
                </div>
            </div>
        @endforeach



        <form action="{{ route('simpleWorkflow.task.create') }}" method="POST" class="p-4 border rounded bg-light">
            @csrf
            <input type="hidden" name="process_id" value="{{ $process->id }}">
            <div class="row mb-3">
                <label for="name" class="col-sm-2 col-form-label">{{ trans('Task Name') }}</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="{{ trans('Enter task name') }}">
                </div>
            </div>
            <div class="row mb-3">
                <label for="type" class="col-sm-2 col-form-label">{{ trans('Task Type') }}</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-select">
                        <option value="form">{{ trans('Form') }}</option>
                        <option value="condition">{{ trans('Condition') }}</option>
                        <option value="script">{{ trans('Script') }}</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Parent Task') }}</label>
                <div class="col-sm-10">
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">{{ trans('None') }}</option>
                        @foreach ($process->tasks() as $task)
                            <option value="{{ $task->id }}">{{ $task->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" class="btn btn-primary">{{ trans('Create') }}</button>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('script')
    <script>
        function create_process() {
            var form = $('#create-process-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.process.create') }}",
                fd,
                function(response) {
                    console.log(response);

                }
            )

        }

        function check_error() {
            send_ajax_get_request(
                "{{ route('simpleWorkflow.process.processHasError', ['processId' => $process->id]) }}",
                function(response) {
                    if (response > 0) {
                        show_error('Process Has Error');
                    } else {
                        show_message('Process Has No Error');
                    }
                }
            )
        }
    </script>
@endsection

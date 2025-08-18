@extends('behin-layouts.app')

@section('content')
<div class="container table-responsive">
    <div class="row">
        <div class="col-md-12">
            <h3>{{ trans('fields.Start Process') }}</h3>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="list-group">
                @foreach ($processes as $process)
                    <span 
                        class="list-group-item list-group-item-action"
                        onclick="if(confirm('شروع؟')) { window.location='{{ route('simpleWorkflow.process.start', [
                            'taskId' => $process->task->id,
                            'inDraft' => true,
                            'force' => 0,
                            'redirect' => true,
                        ]) }}'; }"
                        >
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $process->name }}</h5>
                            <small>{{ trans('fields.Start') }}</small>
                        </div>
                        <p class="mb-1">{{ $process->description }}</p>
                        <small>{{ trans('fields.Start from task') }}: {{ $process->task->name }}</small>
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection


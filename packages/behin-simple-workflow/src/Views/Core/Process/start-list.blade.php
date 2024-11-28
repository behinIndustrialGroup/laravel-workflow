@extends('behin-layouts.app')

@section('content')
<div class="container table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ trans('Id') }}</th>
                <th>{{ trans('Name') }}</th>
                <th>{{ trans('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($processes as $process)
                <tr>
                    <td>{{ $process->id }}</td>
                    <td>{{ $process->name }}</td>
                    <td><a href="{{ route('simpleWorkflow.process.start', ['taskId' => $process->task->id]) }}">{{ trans('Start') }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Expired Tasks') }}
@endsection

@section('content')
    <div class="container">
        <h2>{{ trans('fields.Expired Tasks') }}</h2>
        @if (count($expiredTasks) == 0)
            <div class="alert alert-info">
                {{ trans('fields.No expired tasks found') }}
            </div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('fields.Task Name') }}</th>
                        <th>{{ trans('fields.Case Name') }}</th>
                        <th>{{ trans('fields.Actor') }}</th>
                        <th>{{ trans('fields.Deadline') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expiredTasks as $task)
                        <tr>
                            <td>{{ $task->task->name }}</td>
                            <td>{{ $task->case_name }}</td>
                            <td>{{ getUserInfo($task->actor)->name }}</td>
                            <td>{!! $task->time_status !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
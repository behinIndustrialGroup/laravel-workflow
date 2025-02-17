@extends('behin-layouts.app')

@php
    $tasks = getProcessTasks();
@endphp


@section('title')
    {{ trans('Task Actor List') }}
@endsection

@section('content')
    <div class="container">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <td>{{ trans('Row') }}</td>
                    <td>{{ trans('ID') }}</td>
                    <td>{{ trans('Task ID') }}</td>
                    <td>{{ trans('Task') }}</td>
                    <td>{{ trans('Task Assignment Type') }}</td>
                    <td>{{ trans('Actor') }}</td>
                    <td>{{ trans('Created at') }}</td>
                    <td>{{ trans('Action') }}</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($taskActors as $key => $value)
                    <tr>
                        <td>{{ $key + 1 }} </td>
                        <td>{{ $value->id }}</td>
                        <td>{{ $value->task->id }}</td>
                        <td>{{ $value->task->name }}</td>
                        <td>{{ $value->task->assignment_type }}</td>
                        <td>{{ $value->actor }}</td>
                        <td>{{ $value->created_at }}</td>
                        <td>
                            <form action="{{ route('simpleWorkflow.task-actors.destroy', $value->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button >{{ trans('Delete') }}</button></td>
                            </form>
                    </tr>
                @endforeach
            </tbody>
            <form action="{{ route('simpleWorkflow.task-actors.store') }}" method="POST">
                @csrf
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <select name="task_id" id="">
                                @foreach ($tasks as $task)
                                    @if ($task->type == 'form')
                                        <option value="{{ $task->id }}">{{ $task->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td></td>
                        <td>
                            <input type="text" name="actor" id="">
                        </td>
                        <td><button>{{ trans('Create') }}</button></td>
                    </tr>
                </tfoot>
            </form>
        </table>
    </div>
@endsection

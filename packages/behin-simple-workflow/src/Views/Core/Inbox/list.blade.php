@extends('behin-layouts.app')

@section('content')
<div class="container">
    <h2>{{ trans('User Inbox') }}</h2>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if($rows->isEmpty())
        <div class="alert alert-info">
            {{ trans('You have no items in your inbox.') }}
        </div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('Process Title') }}</th>
                    <th>{{ trans('Task Title') }}</th>
                    <th>{{ trans('Status') }}</th>
                    <th>{{ trans('Received At') }}</th>
                    <th>{{ trans('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->task->process->name }}</td>
                        <td>{{ $row->task->name }}</td>
                        <td>
                            @if($row->status == 'new')
                                <span class="badge bg-primary">{{ trans('New') }}</span>
                            @elseif($row->status == 'in_progress')
                                <span class="badge bg-warning">{{ trans('In Progress') }}</span>
                            @else
                                <span class="badge bg-success">{{ trans('Completed') }}</span>
                            @endif
                        </td>
                        <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('simpleWorkflow.inbox.view', $row->id) }}" class="btn btn-sm btn-primary">{{ trans('View') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

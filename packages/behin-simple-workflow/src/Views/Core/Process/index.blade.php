@extends('behin-layouts.app')

@section('title')
    {{ trans('Process List') }}
@endsection

@section('content')
    <div class="container">
        <a href="{{ route('simpleWorkflow.process.create') }}" class="btn btn-primary">{{ trans('Create') }}</a>
        <table class="table table-strpped">
            <thead>
                <tr>
                    <td>{{ trans('Row') }}</td>
                    <td>{{ trans('ID') }}</td>
                    <td>{{ trans('Name') }}</td>
                    <td>{{ trans('Created at') }}</td>
                    <td>{{ trans('Edit') }}</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($processes as $key => $value)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $value->id }}</td>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->created_at }}</td>
                        <td><a href="{{ route('simpleWorkflow.task.index', $value->id) }}">{{ trans('Edit') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

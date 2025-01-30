@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Script List') }}
@endsection

@section('content')
    <div class="card table-responsive">
        <div class="card-header">
            <h3 class="card-title">{{ trans('fields.Script List') }}</h3>
            <div class="card-tools">
                <a href="{{ route('simpleWorkflow.scripts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ trans('Create New Script') }}
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped projects">
                <thead>
                    <tr>
                        <th style="width: 1%">ID</th>
                        <th>Name</th>
                        <th>Executive File</th>
                        <th style="width: 20%">{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scripts as $script)
                        <tr>
                            <td>{{ $script->id }}</td>
                            <td>{{ $script->name }}</td>
                            <td>{{ $script->executive_file }}</td>
                            <td class="project-actions text-right">
                                <a class="btn btn-primary btn-sm" href="{{ route('simpleWorkflow.scripts.edit', $script->id) }}">
                                    <i class="fas fa-pencil-alt"></i> {{ trans('Edit') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

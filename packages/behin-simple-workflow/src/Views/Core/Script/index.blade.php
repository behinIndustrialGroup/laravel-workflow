@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Script List') }}
@endsection

@section('content')
    <h1>Scripts</h1>
    <div class="row">
        <div class="col-md-6">
            <a href="{{ route('simpleWorkflow.scripts.create') }}" class="btn btn-primary">Create New Script</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Executive File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scripts as $script)
                        <tr>
                            <td>{{ $script->id }}</td>
                            <td>{{ $script->name }}</td>
                            <td>{{ $script->executive_file }}</td>
                            <td>
                                <a href="{{ route('simpleWorkflow.scripts.edit', $script->id) }}"
                                    class="btn btn-primary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

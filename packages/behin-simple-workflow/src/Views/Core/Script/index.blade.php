@extends('behin-layouts.app')

@section('content')
    <h1>Scripts</h1>
    <a href="{{ route('simpleWorkflow.scripts.create') }}">Create New Script</a>
    <ul>
        @foreach ($scripts as $script)
            <li>
                {{ $script->name }}
                <a href="{{ route('simpleWorkflow.scripts.edit', $script->id) }}">Edit</a>
            </li>
        @endforeach
    </ul>
@endsection

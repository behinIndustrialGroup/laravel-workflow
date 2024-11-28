@extends('behin-layouts.app')

@section('content')
    <h1>Edit Script</h1>
    <form action="{{ route('simpleWorkflow.scripts.update', $script->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Name:</label>
        <input type="text" name="name" value="{{ $script->name }}" required>
        <label>Executive File:</label>
        <input type="text" name="executive_file" value="{{ $script->executive_file }}">
        <label>Content (JSON):</label>
        <textarea name="content">{{ $script->content }}</textarea>
        <button type="submit">Update</button>
    </form>
@endsection

@extends('behin-layouts.app')

@section('content')
    <h1>Create Script</h1>
    <form action="{{ route('simpleWorkflow.scripts.store') }}" method="POST">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>Executive File:</label>
        <input type="text" name="executive_file">
        <label>Content (JSON):</label>
        <textarea name="content"></textarea>
        <button type="submit">Create</button>
    </form>
@endsection

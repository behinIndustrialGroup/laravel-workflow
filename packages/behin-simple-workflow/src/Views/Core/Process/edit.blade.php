@extends('behin-layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('simpleWorkflow.process.update', $process->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">{{ trans('Name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $process->name }}">
        </div>
        <div class="form-group">
            <label for="category">{{ trans('Category') }}</label>
            <input type="text" class="form-control" id="category" name="category" value="{{ $process->category }}">
        </div>
        <div class="form-group">
            <label for="case_prefix">{{ trans('Case Prefix') }}</label>
            <input type="text" class="form-control" id="case_prefix" name="case_prefix" value="{{ $process->case_prefix }}">
        </div>
        <button type="submit" class="btn btn-primary">{{ trans('Save') }}</button>
    </form>
</div>
@endsection

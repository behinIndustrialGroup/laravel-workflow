@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Record') }}
@endsection

@section('content')
<div class="container card p-3">
    <form action="{{ route('simpleWorkflow.entities.updateRecord', [$entity->id, $record->id]) }}" method="POST">
        @csrf
        @method('PUT')
        @foreach($columns as $column)
            <div class="mb-3">
                <label class="form-label">{{ trans('fields.' . $column) }}</label>
                <input type="text" name="{{ $column }}" value="{{ $record->$column }}" class="form-control">
            </div>
        @endforeach
        <button class="btn btn-primary">{{ trans('fields.Save') }}</button>
    </form>
</div>
@endsection

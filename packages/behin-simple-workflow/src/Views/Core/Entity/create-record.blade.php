@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Create Record') }}
@endsection

@section('content')
<div class="container card p-3">
    <form action="{{ route('simpleWorkflow.entities.storeRecord', $entity->id) }}" method="POST">
        @csrf
        @foreach($columns as $column)
            <div class="mb-3">
                <label class="form-label">{{ trans('fields.' . $column) }}</label>
                <input type="text" name="{{ $column }}" class="form-control">
            </div>
        @endforeach
        <button class="btn btn-primary">{{ trans('fields.Store') }}</button>
    </form>
</div>
@endsection

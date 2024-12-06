@extends('behin-layouts.app')

@section('content')
    <h1>Edit Field</h1>
    <a href="{{ route('simpleWorkflow.fields.index') }}" class="btn btn-secondary mb-3">
        {{ trans('Back to list') }}
    </a>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @php
        $attributes = json_decode($field->attributes);
    @endphp
    <form action="{{ route('simpleWorkflow.fields.update', $field->id) }}" method="POST" class="p-4 border rounded shadow-sm bg-light">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ trans('Name') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $field->name }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">{{ trans('Type') }}</label>
            <select name="type" id="type" class="form-select">
                <option value="string" @if ($field->type == 'string') selected @endif>{{ trans('String') }}</option>
                <option value="number" @if ($field->type == 'number') selected @endif>{{ trans('Number') }}</option>
                <option value="text" @if ($field->type == 'text') selected @endif>{{ trans('Text') }}</option>
                <option value="date" @if ($field->type == 'date') selected @endif>{{ trans('Date') }}</option>
                <option value="select" @if ($field->type == 'select') selected @endif>{{ trans('Select') }}</option>
                <option value="file" @if ($field->type == 'file') selected @endif>{{ trans('File') }}</option>
                <option value="checkbox" @if ($field->type == 'checkbox') selected @endif>{{ trans('Checkbox') }}</option>
                <option value="radio" @if ($field->type == 'radio') selected @endif>{{ trans('Radio') }}</option>
                <option value="title" @if ($field->type == 'title') selected @endif>{{ trans('Title') }}</option>
                <option value="location" @if ($field->type == 'location') selected @endif>{{ trans('Location') }}</option>
            </select>
        </div>

        @if ($field->type == 'select')
            <div class="mb-3">
                <label for="options" class="form-label">{{ trans('Options') }}</label>
                <span>هر گزینه در یک خط</span>
                <textarea name="options" id="options" class="form-control" rows="4" dir="ltr">{{ isset($attributes?->options) ? $attributes?->options : '' }}</textarea>
            </div>
        @endif

        <div class="mb-3">
            <label for="query" class="form-label">{{ trans('Query') }}</label>
            <p>
                کوئری باید شامل value و label باشد.
            </p>
            <textarea name="query" id="query" class="form-control" rows="4" dir="ltr">{{ is_string($attributes?->query) ? $attributes?->query : '' }}</textarea>
        </div>

        <div class="mb-3">
            <label for="placeholder" class="form-label">{{ trans('Placeholder') }}</label>
            <input type="text" name="placeholder" id="placeholder" class="form-control" value="{{ $attributes?->placeholder }}">
        </div>

        <button class="btn btn-primary">{{ trans('Update') }}</button>
    </form>

@endsection

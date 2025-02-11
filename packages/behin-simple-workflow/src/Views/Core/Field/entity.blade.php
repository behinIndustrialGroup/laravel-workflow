@php
    $attributes = json_decode($field->attributes);
@endphp
{!! Form::text('id', [
    'value' => $attributes?->id ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('columns', [
    'value' => $attributes?->columns ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('query', [
    'value' => $attributes?->query ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}

{!! Form::textarea('script', [
    'value' => $attributes?->script ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}

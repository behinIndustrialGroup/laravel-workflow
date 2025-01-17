@php
    $attributes = json_decode($field->attributes);
@endphp
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

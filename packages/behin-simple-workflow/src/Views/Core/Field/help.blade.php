@php
    $attributes = json_decode($field->attributes);
@endphp
{!! Form::text('id', [
    'value' => $attributes?->id ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('options', [
    'value' => $attributes?->options ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('style', [
    'value' => $attributes?->style ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('script', [
    'value' => $attributes?->script ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}

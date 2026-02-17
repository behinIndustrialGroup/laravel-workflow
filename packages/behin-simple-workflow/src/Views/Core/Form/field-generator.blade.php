@php
    $fieldLabel = trans('SimpleWorkflowLang::fields.' . $fieldName);
    $fieldDetails = getFieldDetailsByName($fieldName);
    if ($fieldDetails) {
        $fieldAttributes = json_decode($fieldDetails->attributes);
    }
    $fieldType = isset($fieldDetails->type) ? $fieldDetails->type : null;
    $fieldStyle = isset($fieldAttributes->style) ? $fieldAttributes->style : null;
    $fieldScript = isset($fieldAttributes->script) ? $fieldAttributes->script : null;
    $fieldPlaceholder = isset($fieldAttributes->placeholder) ? $fieldAttributes->placeholder : null;
    $fieldOptions = isset($fieldAttributes->options) ? $fieldAttributes->options : null;
    $fieldQuery = isset($fieldAttributes->query) ? $fieldAttributes->query : null;
    $fieldDatalist = isset($fieldAttributes->datalist_from_database) ? $fieldAttributes->datalist_from_database : null;
    $fieldClass = isset($fieldAttributes->class) ? $fieldAttributes->class : 'form-control';
@endphp
@if ($fieldDetails->type == 'title')
    {!! Form::title($fieldId, [
        'value' => $fieldValue,
        'class' => '',
        'id' => $fieldId,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'hidden')
    {!! Form::hidden($fieldId, [
        'value' => $fieldValue,
        'class' => '',
        'id' => $fieldId,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'help')
    {!! Form::help($fieldId, [
        'options' => isset($fieldAttributes?->options) ? $fieldAttributes?->options : null,
        'class' => '',
        'id' => $fieldDetails->id ?? $fieldId,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'location')
    @php
        $defaultLat = isset($variables) ? $variables->where('key', $field->fieldName . '_lat')->first()?->value : null;
        $defaultLng = isset($variables) ? $variables->where('key', $field->fieldName . '_lng')->first()?->value : null;
    @endphp
    {!! Form::location($fieldId, [
        'value' => $fieldValue,
        'class' => '',
        'id' => $fieldId,
        'required' => $required,
        'readonly' => $readOnly,
        'defaultZoom' => 13,
        'defaultLat' => $defaultLat,
        'defaultLng' => $defaultLng,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'string')
    {!! Form::text($fieldId, [
        'value' => $fieldValue,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
        'datalist_from_database' => isset($fieldAttributes?->datalist_from_database)
            ? $fieldAttributes?->datalist_from_database
            : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'checkbox')
    {!! Form::checkbox($fieldId, [
        'value' => $fieldValue,
        'class' => '',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'text')
    {!! Form::textarea($fieldId, [
        'value' => $fieldValue,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'date')
    {!! Form::date($fieldId, [
        'value' => $fieldValue,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'time')
    {!! Form::time($fieldId, [
        'value' => $fieldValue,
        'class' => 'form-control timepicker',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'datetime')
    {!! Form::datetime($fieldId, [
        'value' => $fieldValue,
        'altValue' => $fieldValueAlt ?? '',
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'select')
    {!! Form::select($fieldId, is_string($fieldAttributes?->options) ? $fieldAttributes?->options : null, [
        'value' => $fieldValue,
        'query' => is_string($fieldAttributes?->query) ? $fieldAttributes?->query : null,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'select-simple')
    {!! Form::selectSimple($fieldId, is_string($fieldAttributes?->options) ? $fieldAttributes?->options : null, [
        'value' => $fieldValue,
        'query' => is_string($fieldAttributes?->query) ? $fieldAttributes?->query : null,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'select-multiple')
    {!! Form::selectMultiple($fieldId, is_string($fieldAttributes?->options) ? $fieldAttributes?->options : null, [
        'value' => json_decode($fieldValue),
        'query' => is_string($fieldAttributes?->query) ? $fieldAttributes?->query : null,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type === 'searchable-input')
    {!! Form::searchableInput($fieldId, [
        'value' => $fieldValue,
        'endpoint' => isset($fieldAttributes->endpoint) && is_string($fieldAttributes->endpoint)
            ? $fieldAttributes->endpoint
            : null,
        'minChars' => isset($fieldAttributes->minChars) ? $fieldAttributes->minChars : null,
        'limit' => isset($fieldAttributes->limit) ? $fieldAttributes->limit : null,
        'initial_label' => $fieldAttributes->initial_label ?? ($fieldAttributes->initialLabel ?? null),
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldPlaceholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => $fieldStyle,
        'script' => $fieldScript,
    ]) !!}
@endif
@if ($fieldDetails->type == 'file')
    {{-- @php
        $fieldValues = isset($variables) ? $variables->where('key', $field->fieldName)->pluck('value') : [];
    @endphp --}}
    {!! Form::file($fieldId, [
        'value' => $fieldValue ?? [],
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'signature')
    {!! Form::signature($fieldId, [
        'value' => $fieldValue,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
        'datalist_from_database' => isset($fieldAttributes?->datalist_from_database)
            ? $fieldAttributes?->datalist_from_database
            : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'entity')
    {!! Form::entity($fieldId, [
        'columns' => is_string($fieldAttributes?->columns) ? $fieldAttributes?->columns : null,
        'query' => is_string($fieldAttributes?->query) ? $fieldAttributes?->query : null,
        'class' => 'form-control',
        'id' => $fieldAttributes?->id,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'button')
    {!! Form::button($fieldName, [
        'class' => $fieldClass,
        'id' => $fieldAttributes?->id ?? $fieldName,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
    ]) !!}
@endif
@if ($fieldDetails->type == 'view-model')
    {!! Form::viewModel($fieldId, [
        'class' => $fieldClass,
        'id' => $fieldId,
        'view_model_id' => $fieldAttributes?->view_model_id,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
    ]) !!}
@endif

@if ($fieldDetails->type == 'formatted-digit')
    {!! Form::formattedDigit($fieldId, [
        'value' => $fieldValue,
        'class' => 'form-control',
        'id' => $fieldId,
        'placeholder' => $fieldAttributes?->placeholder,
        'required' => $required,
        'readonly' => $readOnly,
        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
        'datalist_from_database' => isset($fieldAttributes?->datalist_from_database)
            ? $fieldAttributes?->datalist_from_database
            : null,
    ]) !!}
@endif

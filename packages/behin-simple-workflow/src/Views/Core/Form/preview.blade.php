@php
    $mode = isset($mode) ? $mode : null;
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
@endphp
<script src="{{ url('packages/behin-form-builder/src/js/signature_pad.umd.min.js') }}"></script>
<div class="row col-sm-12 p-0 m-0 dynamic-form" id="{{ $form->id }}">
    @foreach ($content as $field)
        @php
            $fieldLabel = trans('SimpleWorkflowLang::fields.' . $field->fieldName);
            $fieldClass = $field->class;
            $fieldId = $field->fieldName;
            $required = $field->required;
            $readOnly = $mode ? $mode : $field->readOnly;
            $fieldDetails = getFieldDetailsByName($field->fieldName);
            if ($fieldDetails) {
                $fieldAttributes = json_decode($fieldDetails->attributes);
                $fieldValue = isset($variables) ? $variables->where('key', $field->fieldName)->first()?->value : null;
            } else {
                if ($field->fieldName != $form->id) {
                    $childForm = getFormInformation($field->fieldName);
                }
            }
        @endphp

        @if ($fieldDetails)
            <div class="{{ $field->class }}">
                @if ($fieldDetails->type == 'title')
                    {!! Form::title($fieldId, [
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
                        'id' => $fieldId,
                        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
                        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
                    ]) !!}
                @endif
                @if ($fieldDetails->type == 'location')
                    @php
                        $defaultLat = isset($variables)
                            ? $variables->where('key', $field->fieldName . '_lat')->first()?->value
                            : null;
                        $defaultLng = isset($variables)
                            ? $variables->where('key', $field->fieldName . '_lng')->first()?->value
                            : null;
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
                        'class' => 'form-control persian-date',
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
                @if ($fieldDetails->type == 'file')
                    @php
                        $fieldValues = isset($variables)
                            ? $variables->where('key', $field->fieldName)->pluck('value')
                            : [];
                    @endphp
                    {!! Form::file($fieldId, [
                        'value' => $fieldValues,
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
                    {!! Form::button($fieldId, [
                        'class' => $field->class,
                        'id' => $fieldId,
                        'style' => isset($fieldAttributes?->style) ? $fieldAttributes?->style : null,
                        'script' => isset($fieldAttributes?->script) ? $fieldAttributes?->script : null,
                    ]) !!}
                @endif
            </div>
        @else
            @isset($childForm)
                @include('SimpleWorkflowView::Core.Form.preview', ['form' => $childForm, 'mode' => $field->readOnly])
            @endisset
        @endisset
    @endforeach
</div>

{!! $form->scripts !!}

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
            $fieldName = $field->fieldName;
            $fieldClass = $field->class;
            $fieldId = $field->fieldName;
            $required = $field->required;
            $readOnly = $mode ? $mode : $field->readOnly;
            $fieldDetails = getFieldDetailsByName($field->fieldName);
            if ($fieldDetails) {
                $fieldAttributes = json_decode($fieldDetails->attributes);
                $fieldValue = isset($case) ? $case->getVariable($field->fieldName) : null;
            } else {
                if ($field->fieldName != $form->id) {
                    $childForm = getFormInformation($field->fieldName);
                }
            }
        @endphp

        @if ($fieldDetails)
            <div class="{{ $fieldClass }}">
                @include('SimpleWorkflowView::Core.Form.field-generator', [
                    'fieldName' => $fieldName,
                    'fieldId' => $fieldId,
                    'fieldClass' => $fieldClass,
                    'readOnly' => $readOnly,
                    'required' => $required,
                    'fieldValue' => $fieldValue,
                ])
            </div>
        @else
            @isset($childForm)
                @include('SimpleWorkflowView::Core.Form.preview', [
                    'form' => $childForm,
                    'mode' => $readOnly,
                ])
            @endisset
        @endif
    @endforeach
</div>

{!! $form->scripts !!}

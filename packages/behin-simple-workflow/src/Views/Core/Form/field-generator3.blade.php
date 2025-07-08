@php
    $fieldLabel = trans('SimpleWorkflowLang::fields.' . $fieldName);
    $fieldDetails = getFieldDetailsByName($fieldName);
    if ($fieldDetails) {
        $fieldAttributes = json_decode($fieldDetails->attributes);
        if($fieldValue == null){
            $fieldValue = isset($variables) ? $variables->where('key', $fieldName)->first()?->value : null;
        }
    } else {
        if ($fieldName != $form->id) {
            $childForm = getFormInformation($fieldName);
        }
    }
@endphp
@if ($fieldDetails)
    <div class="{{ $fieldClass }}">
        @include('SimpleWorkflowView::Core.Form.field-generator2', [
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

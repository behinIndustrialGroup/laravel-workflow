@php
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
    $task = $inbox->task ?? '';
@endphp
@if ($modalShow)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="mb-0" style="float: right">
                <button class="btn btn-sm btn-secondary">{{ trans('fields.Case Number') }}: {{ $case->number }}</button>
            </p>
            <p class="mb-0" style="float: left">
                <button class="btn btn-sm btn-secondary"
                    onclick="close_admin_modal()">{{ trans('fields.close') }}</button>
            </p>
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="mb-4">
    @if ($modalShow)
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $form->name }}</h6>
        </div>
    @endif
    <div class="card-body">
            @if (View::exists('SimpleWorkflowView::Custom.Form.' . $form->id))
                @include('SimpleWorkflowView::Custom.Form.' . $form->id, [
                    'form' => $form,
                    'case' => $case,
                ])
            @else
                <div class="row col-sm-12 p-0 m-0 dynamic-form" id="{{ $form->id }}">
                    @foreach ($content as $field)
                        @php
                            $fieldLabel = trans('SimpleWorkflowLang::fields.' . $field->fieldName);
                            $fieldName = $field->fieldName;
                            $fieldClass = $field->class;
                            $fieldId = $field->fieldName;
                            $required = $field->required;
                            $readOnly = $field->readOnly;
                            $fieldDetails = getFieldDetailsByName($field->fieldName);
                            if ($fieldDetails) {
                                $fieldAttributes = json_decode($fieldDetails->attributes);
                                $fieldValue = $row->$fieldName ?? '';
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
                                    'readOnly' => 'on',
                                    'required' => $required,
                                    'fieldValue' => $fieldValue,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>

            @endif
    </div>
</div>
<script>
    initial_view()

</script>

{!! $form->scripts !!}

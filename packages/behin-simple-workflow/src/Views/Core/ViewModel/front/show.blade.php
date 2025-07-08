@php
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
    $task = $inbox->task ?? '';
@endphp
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <p class="mb-0" style="float: right">
            <button class="btn btn-sm btn-secondary">{{ trans('fields.Case Number') }}: {{ $case->number }}</button>
        </p>
        <p class="mb-0" style="float: left">
            <button class="btn btn-sm btn-secondary" onclick="close_admin_modal()">{{ trans('fields.close') }}</button>
        </p>
    </div>
</div>
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $form->name }}</h6>
    </div>
    <div class="card-body">
        <form action="javascript:void(0)" method="POST" id="modal-form"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="inboxId" id="inboxId" value="{{ $inbox->id ?? '' }}">
            <input type="hidden" name="caseId" id="caseId" value="{{ $case->id }}">
            <input type="hidden" name="viewModelId" id="viewModelId" value="{{ $viewModel->id }}">
            <input type="hidden" name="rowId" id="rowId" value="{{ $row->id ?? '' }}">
            <input type="hidden" name="api_key" id="api_key" value="{{ $viewModel->api_key }}">
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
                                    'readOnly' => $readOnly,
                                    'required' => $required,
                                    'fieldValue' => $fieldValue,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
                <button class="btn btn-sm btn-success" onclick="updateViewModelRecord()">{{ trans('fields.Save') }}</button>

            @endif
        </form>
    </div>
</div>
<script>
    function updateViewModelRecord(){
        var fd = new FormData($('#modal-form')[0]);
        var url = "{{ route('simpleWorkflow.view-model.update-record') }}"
        send_ajax_formdata_request(url, fd, function(response){
            show_message(response)
            get_view_model_rows('{{ $viewModel->id }}', '{{ $viewModel->api_key }}')
        })
    }
</script>

{!! $form->scripts !!}

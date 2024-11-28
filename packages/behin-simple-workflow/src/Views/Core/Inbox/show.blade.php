@extends('behin-layouts.app')

@php
    $content = json_decode($form->content);
@endphp

@section('content')
    <form action="" method="POST" id="form">
        <div class="row">
            <input type="hidden" name="inboxId" id="" value="{{ $inbox->id }}">
            <input type="hidden" name="caseId" id="" value="{{ $case->id }}">
            <input type="hidden" name="taskId" id="" value="{{ $task->id }}">
            <input type="hidden" name="processId" id="" value="{{ $process->id }}">
            @foreach ($content as $field)
                @php
                    $fieldLabel = trans('SimpleWorkflowLang::fields.' . $field->fieldName);
                    $fieldClass = $field->class;
                    $fieldId = $field->fieldName;
                    $fieldDetails = getFieldDetailsByName($field->fieldName);
                    $fieldAttributes = json_decode($fieldDetails->attributes);
                    $fieldValue = $variables->where('key', $field->fieldName)->first()?->value;
                @endphp
                <div class="{{ $field->class }}">
                    @if ($fieldDetails->type == 'string')
                        {!! Form::text($fieldId, [
                            'value' => $fieldValue,
                            'class' => 'form-control',
                            'id' => $fieldId,
                            'placeholder' => $fieldAttributes?->placeholder,
                        ]) !!}
                    @endif
                    @if ($fieldDetails->type == 'date')
                        {!! Form::date($fieldId, [
                            'value' => $fieldValue,
                            'class' => 'form-control',
                            'id' => $fieldId,
                            'placeholder' => $fieldAttributes?->placeholder,
                        ]) !!}
                    @endif
                </div>
            @endforeach
        </div>
    </form>

    <div class="row">
        <button class="btn btn-primary" onclick="saveForm()">{{ trans('Save') }}</button>
        <button class="btn btn-primary" onclick="saveAndNextForm()">{{ trans('Save and next') }}</button>
    </div>
@endsection

@section('script')
    <script>
        function saveForm() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.save') }}',
                fd,
                function(response) {
                    console.log(response);

                }
            )
        }

        function saveAndNextForm() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.saveAndNext') }}',
                fd,
                function(response) {
                    console.log(response);

                }
            )
        }
    </script>
@endsection

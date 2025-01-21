@extends('behin-layouts.app')

@php
    $variables = $case->variables();

@endphp

@section('title')
    گزارش: {{ $case->getVariable('customer_name') }}
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">گزارش: {{ $case->getVariable('customer_fullname') }}</div>
                    <div class="card-body">
                        <div class="table-responsive" id="body">
                            <form action="javascript:void(0)" method="POST" id="form">
                                <input type="hidden" name="caseId" id="" value="{{ $case->id }}">
                                <input type="hidden" name="processId" id="" value="{{ $process->id }}">
                                @include('SimpleWorkflowView::Core.Form.preview', [
                                    'form' => $form,
                                    'case' => $case,
                                    'variables' => $variables,
                                    'process' => $process,
                                ])
                            </form>

                            {{-- <table class="table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>نوع</th>
                                        <th>متغیر</th>
                                        <th>مقدار</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variables as $variable)
                                        <tr>
                                            @php
                                                $varDetails = getVariableDetailsByName($variable->key);
                                                $type = isset($varDetails['type']) ? $varDetails['type'] : '';
                                            @endphp
                                            <td>{{ $type }}</td>
                                            <td dir="auto">{{ trans('fields.' . $variable->key) }}</td>
                                            <td class="text-right" style="white-space: pre-wrap;">
                                                @if ($varDetails->type == 'file')
                                                    @php
                                                        $fieldValues = isset($variables)
                                                            ? $variables
                                                                ->where('key', $field->fieldName)
                                                                ->pluck('value')
                                                            : [];
                                                    @endphp
                                                    {!! Form::file($fieldId, [
                                                        'value' => $fieldValues,
                                                        'class' => 'form-control',
                                                        'id' => $fieldId,
                                                        'placeholder' => $fieldAttributes?->placeholder,
                                                        'required' => $required,
                                                        'readonly' => $readOnly,
                                                    ]) !!}
                                                @endif
                                                {{ $variable->value }}
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end bg-white p-2 mt-2">
            <button class="btn btn-sm btn-outline-primary m-1" onclick="saveForm()">
                <i class="fa fa-save"></i> {{ trans('fields.Save') }}
            </button>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view()

        function saveForm() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.save') }}',
                fd,
                function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        show_message(response.msg)
                        window.location.reload();
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }
    </script>
@endsection

{{-- @section('script') --}}
{{-- <script>
        function changeForm(){

            $('#body').html(`{{ view('SimpleWorkflowView::Core.Form.preview', ['form' => $form,'case' => $case,'variables' => $variables,'process' => $process]) }}`);
        }
    </script> --}}

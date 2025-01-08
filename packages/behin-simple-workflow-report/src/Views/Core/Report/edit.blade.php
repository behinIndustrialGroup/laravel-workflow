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
                            @include('SimpleWorkflowView::Core.Form.preview', [
                                'form' => $form,
                                'case' => $case,
                                'variables' => $variables,
                                'process' => $process,
                            ])
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
    </div>
@endsection
@section('script')
    <script>
        function changeForm(){

            $('#body').html(`{{ view('SimpleWorkflowView::Core.Form.preview', ['form' => $form,'case' => $case,'variables' => $variables,'process' => $process]) }}`);
        }
    </script>

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
                    <div class="card-header">متغیرها</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>کلید</th>
                                    <th>مقدار</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($variables as $variable)
                                    <tr>
                                        <td>{{ trans( 'SimpleWorkflowLang::fields.'. $variable->key) }}</td>
                                        <td>{{ $variable->value }}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

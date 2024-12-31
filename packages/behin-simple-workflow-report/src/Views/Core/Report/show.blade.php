@extends('behin-layouts.app')


@section('title')

@endsection


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">لیست موارد فرآیند</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>شناسه</th>
                                    <th>شماره مورد</th>
                                    <th>ایجاد کننده</th>
                                    <th>نام</th>
                                    <th>ایجاد شده در</th>
                                    <th>اقدام</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($process->cases as $case)
                                    <tr>
                                        <td>{{ $case->id }}</td>
                                        <td>{{ $case->number }}</td>
                                        <td>{{ $case->creator()->name }}</td>
                                        @php
                                            $s = $case->variables()->where('key', 'customer_name')->first()?->value;
                                            $s .= ' - ';
                                            $s .= $case->variables()->where('key', 'customer_mobile')->first()?->value;
                                        @endphp
                                        <td>{{ $s }}</td>
                                        <td>{{ $case->created_at }}</td>
                                        <td><a href="{{ route('simpleWorkflowReport.report.edit', [ 'report' => $case->id ]) }}">{{ 'show' }}</a></td>
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

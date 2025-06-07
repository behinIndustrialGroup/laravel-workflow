@extends('behin-layouts.app')

@section('title', 'گزارش چک ها')

@section('content')
    <div>
        <div class="card">
            <div class="card-header bg-info text-center">
                <h3>گزارش چک ها</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>شماره پرونده</th>
                            <th>مبلغ</th>
                            <th>تاریخ</th>
                            <th>توضیحات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cheques as $cheque)
                            <tr>
                                <td>
                                    <a
                                        href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $cheque->case_number]) }}"><i
                                            class="fa fa-external-link"></i></a>
                                    {{ $cheque->case_number }}
                                </td>
                                <td>{{ number_format($cheque->cost) }}</td>
                                <td>{{ toJalali((int)$cheque->cheque_due_date)->format('Y-m-d') }}</td>
                                <td>{{ $cheque->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


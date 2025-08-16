@extends('behin-layouts.app')

@section('title', 'گزارش حساب دفتری')


@php
    $disableBackBtn = true;
    use Behin\SimpleWorkflow\Models\Entities\Financials;
@endphp

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <a href="javascript:history.back()" class="btn btn-outline-primary float-left">
                <i class="fa fa-arrow-left"></i> {{ trans('fields.Back') }}
            </a>
        </div>
    </div>
    <div class="card table-responsive">
        <div class="card-header bg-secondary text-center">
            <h3 class="card-title">گزارش بدهکاران در مرحله دریافت هزینه</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="inbox-list">
                <thead>
                    <tr>
                        <th>شماره پرونده</th>
                        <th>نام مشتری</th>
                        <th>مبلغ</th>
                        <th>تاریخ</th>
                        <th>توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inboxes as $inbox)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $inbox->case->number]) }}">
                                    <i class="fa fa-external-link"></i>
                                </a>
                                {{ $inbox->case->number }}
                            </td>
                            <td>{{ $inbox->case->getVariable('customer_workshop_or_ceo_name') }}</td>
                            <td>{{ number_format(Financials::where('case_number', $inbox->case->number)->sum('cost')) }}
                            </td>
                            <td>{{ toJalali((int) $inbox->created_at)->format('Y-m-d') }}</td>
                            <td>{{ $inbox->case_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="on-credit-list">
                <thead>
                    <tr>
                        <th>شماره پرونده</th>
                        <th>نام مشتری</th>
                        <th>مبلغ</th>
                        <th>تاریخ</th>
                        <th>توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($onCredits as $onCredit)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $onCredit->case_number]) }}">
                                    <i class="fa fa-external-link"></i>
                                </a>
                                {{ $onCredit->case_number }}
                            </td>
                            <td>{{ $onCredit->case()->getVariable('customer_workshop_or_ceo_name') }}</td>
                            <td>{{ number_format($onCredit->cost) }}</td>
                            <td>{{ toJalali((int) $onCredit->fix_cost_date)->format('Y-m-d') }}</td>



                            <td>{{ $onCredit->description }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#inbox-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                var intVal = function(i) {
                    if (typeof i === 'string') {
                        return parseInt(i.replace(/,/g, '')) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };

                var pageTotal = 0;

                api.rows({
                    page: 'current'
                }).every(function(rowIdx, tableLoop, rowLoop) {
                    var amount = this.data()[2]; // ستون مبلغ
                    pageTotal += intVal(amount);
                });

                // نمایش در فوتر
                $(api.column(2).footer()).html(
                    pageTotal.toLocaleString('fa-IR') + ' ریال'
                );
            }
        });

        $('#on-credit-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection

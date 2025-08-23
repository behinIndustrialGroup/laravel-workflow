@extends('behin-layouts.app')

@section('title', 'گزارش حساب دفتری')


@php
    $disableBackBtn = true;
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
            <h3 class="card-title">گزارش حساب دفتری</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="on-credit-list">
                <thead>
                    <tr>
                        <th>شماره پرونده</th>
                        <th>نام مشتری</th>
                        <th>مبلغ</th>
                        <th>تاریخ اعلام صورت حساب</th>
                        <th>شماره فاکتور</th>
                        <th>نقدی/چک</th>
                        <th>تاریخ تسویه/چک</th>
                        <th>توضیحات</th>
                        <th>تسویه شد</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalCost = 0;
                    @endphp
                    @foreach ($onCredits as $onCredit)
                        <tr @if ($onCredit->is_passed) style="background-color: #d4edda;" class="passed" @endif>
                            <td>
                                <a
                                    href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $onCredit->case_number]) }}">
                                    <i class="fa fa-external-link"></i>
                                </a>
                                {{ $onCredit->case_number }}
                            </td>
                            <td>{{ $onCredit->case()->getVariable('customer_workshop_or_ceo_name') }}</td>
                            <td>
                                @php
                                    $cost = (int) str_replace(',', '', $onCredit->cost);
                                    if (!$onCredit->is_passed) {
                                        $totalCost += $cost;
                                    }
                                @endphp
                                {{ number_format($onCredit->cost) }}
                            </td>
                            <td>{{ toJalali((int) $onCredit->fix_cost_date)->format('Y-m-d') }}</td>
                            <td>
                                @foreach ($onCredit->payments as $payment)
                                    {{ $payment->invoice_number }}
                                    <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($onCredit->payments as $payment)
                                    {{ $payment->payment_type }}
                                    <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($onCredit->payments as $payment)
                                    @if ($payment->payment_type == 'نقدی')
                                        {{ toJalali((int) $payment->date)->format('Y-m-d') }}
                                    @else
                                        {{ toJalali((int) $payment->cheque_due_date)->format('Y-m-d') }}
                                    @endif
                                    <br>
                                @endforeach
                            </td>
                            <td>{{ $onCredit->description }}</td>

                            {{-- دکمه پاس شد --}}
                            <td>
                                <button class="btn btn-sm"
                                    onclick="open_admin_modal('{{ route('simpleWorkflowReport.on-credit-report.edit', $onCredit->id) }}')">
                                    ویرایش
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right">جمع این صفحه:</th>
                        <th id="page-total"></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer bg-secondary">
            <div class="row">
                <div class="col-md-6">
                    مجموع کل تسویه نشده ها: {{ number_format($totalCost) }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@section('script')
    <script>
        $('.settlement_date').persianDatepicker({
            viewMode: 'day',
            initialValue: false,
            format: 'YYYY-MM-DD',
            initialValueType: 'persian',
            calendar: {
                persian: {
                    leapYearMode: 'astronomical',
                    locale: 'fa'
                }
            }
        });
        $('.invoice_date').persianDatepicker({
            viewMode: 'day',
            initialValue: false,
            format: 'YYYY-MM-DD',
            initialValueType: 'persian',
            calendar: {
                persian: {
                    leapYearMode: 'astronomical',
                    locale: 'fa'
                }
            }
        });
        $(document).ready(function() {
            $('#on-credit-list').DataTable({
                pageLength: 25,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    var intVal = function(i) {
                        if (typeof i === 'string') {
                            return parseInt(i.replace(/,/g, '')) || 0;
                        }
                        return typeof i === 'number' ? i : 0;
                    };

                    var settledTotal = 0;
                    var unsettledTotal = 0;
                    var settledCount = 0;
                    var unsettledCount = 0;

                    api.rows({
                        page: 'current'
                    }).every(function(rowIdx, tableLoop, rowLoop) {
                        var rowNode = $(this.node());
                        var amount = this.data()[2]; // ستون مبلغ
                        var cost = intVal(amount);

                        // بررسی بر اساس کلاس ردیف (passed یعنی تسویه شده)
                        if (rowNode.hasClass('passed')) {
                            settledTotal += cost;
                            settledCount++;
                        } else {
                            unsettledTotal += cost;
                            unsettledCount++;
                        }
                    });

                    // نمایش در فوتر
                    $(api.column(2).footer()).html(
                        `<div>
            <span>تسویه شده: ${settledTotal.toLocaleString('fa-IR')} ریال (تعداد: ${settledCount})</span><br>
            <span>تسویه نشده: ${unsettledTotal.toLocaleString('fa-IR')} ریال (تعداد: ${unsettledCount})</span>
        </div>`
                    );
                }

            });
        });
    </script>
@endsection

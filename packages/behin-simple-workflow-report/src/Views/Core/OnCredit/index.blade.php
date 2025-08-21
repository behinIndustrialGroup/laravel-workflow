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
                        {{-- <th>تاریخ تسویه</th>
                        <th>تسویه مطابق شماره فاکتور</th>
                        <th>تاریخ فاکتور</th> --}}
                        <th>توضیحات</th>
                        <th>تسویه شد</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalCost = 0;
                    @endphp
                    @foreach ($onCredits as $onCredit)
                        <tr @if ($onCredit->is_passed) style="background-color: #d4edda;" @endif
                            class="{{ $onCredit->is_passed ? 'settled' : 'unsettled' }}">
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
                            {{-- <td>
                                <form action="{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" id="settlement_date" class="settlement_date"
                                        name="settlement_date" value="{{ $onCredit->settlement_date }}">
                            </td>
                            <td>
                                <input type="text" id="invoice_number" name="invoice_number"
                                    value="{{ $onCredit->invoice_number }}">
                            </td>
                            <td>
                                <input type="text" id="invoice_date" name="invoice_date" class="invoice_date"
                                    value="{{ $onCredit->invoice_date }}">
                                <button type="submit" class="btn btn-sm btn-primary">ذخیره</button>
                                </form>
                            </td> --}}
                            <td>{{ $onCredit->description }}</td>

                            {{-- دکمه پاس شد --}}
                            <td>
                                @if ($onCredit->is_passed)
                                    {{-- <span class="badge bg-success">تسویه شد</span> --}}
                                @else
                                    {{-- <form method="POST"
                                        action="{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}"
                                        onsubmit="return confirm('آیا از تسویه شدن این حساب دفتری مطمئن هستید؟')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_passed" value="1">
                                        <button type="submit" class="btn btn-sm btn-success">تسویه شد</button>
                                    </form> --}}
                                    <button class="btn btn-sm"
                                        onclick="open_admin_modal('{{ route('simpleWorkflowReport.on-credit-report.edit', $onCredit->id) }}')">
                                        ویرایش
                                    </button>
                                @endif
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

                    let settledSum = 0,
                        unsettledSum = 0;
                    let settledCount = 0,
                        unsettledCount = 0;

                    api.rows({
                        page: 'current'
                    }).every(function(rowIdx, tableLoop, rowLoop) {
                        let amount = this.data()[2]; // ستون مبلغ (متن قالب بندی شده)
                        let numAmount = intVal(amount);
                        let $row = $(this.node());

                        // چک کردن وضعیت: اگر رنگ سبز (یا مثلاً کلاس is_passed) داشته باشه
                        let isSettled = $row.hasClass("settled");
                        // 👆 چون توی Blade برای تسویه‌شده‌ها background-color: #d4edda گذاشتی

                        if (isSettled) {
                            settledSum += numAmount;
                            settledCount++;
                        } else {
                            unsettledSum += numAmount;
                            unsettledCount++;
                        }
                    });

                    // نمایش در فوتر
                    $(api.column(2).footer()).html(
                        `
                <div>
                    <span class="text-success">تسویه شده: ${settledSum.toLocaleString('fa-IR')} (${settledCount} مورد)</span><br>
                    <span class="text-danger">تسویه نشده: ${unsettledSum.toLocaleString('fa-IR')} (${unsettledCount} مورد)</span>
                </div>
                `
                    );
                }
            });
        });
    </script>
@endsection

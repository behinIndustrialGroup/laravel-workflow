@extends('behin-layouts.app')

@section('title', 'گزارش درآمدها')
@php
    use Morilog\Jalali\Jalalian;
@endphp

@section('content')
    <div class="card">
        <div class="card-body">
            <form class="row" method="GET">
                <div class="col-md-3">
                    <select name="month" id="month-filter" class="form-select form-control">
                        @foreach ($monthOptions as $option)
                            <option value="{{ $option['value'] }}" data-from="{{ $option['from'] }}"
                                data-to="{{ $option['to'] }}" {{ $selectedMonth === $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="from" class="form-control persian-date" value="{{ $fromValue }}"
                        placeholder="از تاریخ">
                </div>
                <div class="col-md-3">
                    <input type="text" name="to" class="form-control persian-date" value="{{ $toValue }}"
                        placeholder="تا تاریخ">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-warning" type="submit">فیلتر</button>
                    <a href="{{ route('simpleWorkflowReport.petty-cash.export', ['from' => $fromValue, 'to' => $toValue, 'month' => $selectedMonth]) }}"
                        class="btn btn-success">خروجی اکسل</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-3">

        <div class="card-body table-responsive">
            <table class="table table-bordered" id="incomes-table">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>شماره پرونده</th>
                        <th>طرف حساب (واریز کننده / بستانکار)</th>
                        <th>مبلغ</th>
                        <th>تاریخ ثبت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $item)
                        <tr>
                            <td>[[ $item->id ]]</td>
                            <td>[[ $item->case_number ]]</td>
                            <td>[[ $item->counterparty()->name ]]
                                <span class="badge">[[ $item->description ]]</span>
                            </td>
                            <td>[[ number_format($item->amount) ]]</td>
                            <td>[[ toJalali($item->updated_at)->format('Y-m-d') ]]</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th style="text-align:right">جمع:</th>
                        <th></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#incomes-table').DataTable({
            language: {
                url: '{{ url('behin/behin-dist/plugins/datatables/fa.json') }}'
            },
            order: [
                [4, "desc"]
            ],
            pageLength: 10,
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // تبدیل مقادیر به عدد
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[^\d\-\.]/g, '') * 1 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                // جمع کل (ستون مبلغ = index 1)
                var total = api
                    .column(3, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // جمع صفحه جاری
                var pageTotal = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // نمایش در فوتر
                $(api.column(1).footer()).html(
                    'صفحه: ' + pageTotal.toLocaleString() +
                    '<br>کل: ' + total.toLocaleString()
                );
            }
        });

        $(function() {
            var monthFilter = document.getElementById('month-filter');
            var fromInput = document.querySelector('input[name="from"]');
            var toInput = document.querySelector('input[name="to"]');

            function applySelectedMonth() {
                if (!monthFilter) {
                    return;
                }
                var selectedOption = monthFilter.options[monthFilter.selectedIndex];
                if (!selectedOption) {
                    return;
                }
                if (fromInput && selectedOption.dataset.from) {
                    fromInput.value = selectedOption.dataset.from;
                }
                if (toInput && selectedOption.dataset.to) {
                    toInput.value = selectedOption.dataset.to;
                }
            }

            if (monthFilter) {
                monthFilter.addEventListener('change', applySelectedMonth);
            }
        });
    </script>
@endsection

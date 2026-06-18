@extends('behin-layouts.app')

@section('title', 'گزارش تنخواه')
@php
    use Morilog\Jalali\Jalalian;
@endphp

@section('content')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('petty-cash.store') }}" class="row">
                @csrf
                <div class="col-md-3">
                    <input name="title" class="form-control" placeholder="عنوان خرج" required>
                </div>
                <div class="col-md-2">
                    <input name="amount" class="form-control formatted-digit" placeholder="مبلغ" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="paid_at" class="form-control persian-date" placeholder="تاریخ پرداخت"
                        required>
                </div>
                <div class="col-md-2">
                    <input name="case_number" class="form-control" placeholder="پرونده">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary" type="submit">افزودن</button>
                </div>
            </form>
        </div>
    </div>
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

        <div class="card-body">
            <table class="table table-bordered" id="petty-cash-table">
                <thead>
                    <tr>
                        <th>عنوان خرج</th>
                        <th>مبلغ</th>
                        <th>تاریخ پرداخت</th>
                        <th>پرونده</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pettyCashes as $item)
                        <tr data-financial-type="{{ $item->financial_type }}"
                            @if ($item->financial_type == 'بدهکار') style="background: #f56c6c"
                            @elseif ($item->financial_type == 'بستانکار') 
                                class="bg-success" @endif>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->amount) }}</td>
                            <td>{{ $item->transaction_or_cheque_due_date }}</td>
                            <td>{{ $item->case_number }}</td>
                            <td>
                                @if ($item->financial_type == 'بستانکار')
                                    <a href="{{ route('simpleWorkflowReport.petty-cash.edit', $item) }}"
                                        class="btn btn-sm btn-primary">ویرایش</a>
                                    <form action="{{ route('petty-cash.delete', $item->id) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('حذف شود؟')">حذف</button>
                                    </form>
                                @endif
                            </td>
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
    <div class="card">
        <div class="card-body">
            مانده حساب: [[ number_format($debtsPettyCashCounterParty - $creditsPettyCashCounterParty) ]]
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#petty-cash-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fa.json'
            },
            order: [
                [2, "desc"]
            ],
            pageLength: 25,
            footerCallback: function(row, data, start, end, display) {

                var api = this.api();

                function parseNumber(value) {
                    return parseInt(
                        value.toString().replace(/,/g, '')
                    ) || 0;
                }

                let total = 0;
                let pageTotal = 0;

                // جمع کل
                api.rows({
                    search: 'applied'
                }).every(function() {

                    let rowNode = this.node();
                    let financialType = $(rowNode).data('financial-type');

                    let amount = parseNumber(this.data()[1]);

                    if (financialType === 'بستانکار') {
                        total -= amount;
                    } else {
                        total += amount;
                    }
                });

                // جمع صفحه جاری
                api.rows({
                    search: 'applied',
                    page: 'current'
                }).every(function() {

                    let rowNode = this.node();
                    let financialType = $(rowNode).data('financial-type');

                    let amount = parseNumber(this.data()[1]);

                    if (financialType === 'بستانکار') {
                        pageTotal -= amount;
                    } else {
                        pageTotal += amount;
                    }
                });

                $(api.column(1).footer()).html(
                    'مانده صفحه: ' + pageTotal.toLocaleString() +
                    '<br>مانده کل: ' + total.toLocaleString()
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

@extends('behin-layouts.app')

@section('title', 'گزارش تنخواه')
@php
    use Morilog\Jalali\Jalalian;
@endphp

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card mb-3">
        <div class="card-header">
            <form class="row" method="GET">
                <div class="col-md-3">
                    <select name="month" id="month-filter" class="form-select form-control">
                        @foreach ($monthOptions as $option)
                            <option value="{{ $option['value'] }}" data-from="{{ $option['from'] }}" data-to="{{ $option['to'] }}" {{ $selectedMonth === $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="from" class="form-control persian-date" value="{{ $fromValue }}" placeholder="از تاریخ">
                </div>
                <div class="col-md-3">
                    <input type="text" name="to" class="form-control persian-date" value="{{ $toValue }}" placeholder="تا تاریخ">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-warning" type="submit">فیلتر</button>
                    <a href="{{ route('simpleWorkflowReport.petty-cash.export', ['from' => $fromValue, 'to' => $toValue, 'month' => $selectedMonth]) }}" class="btn btn-success">خروجی اکسل</a>
                </div>
            </form>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('simpleWorkflowReport.petty-cash.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-3">
                    <input name="title" class="form-control" placeholder="عنوان خرج" required>
                </div>
                <div class="col-md-2">
                    <input name="amount" class="form-control formatted-digit" placeholder="مبلغ" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="paid_at" class="form-control persian-date" placeholder="تاریخ پرداخت" required>
                </div>
                <div class="col-md-3">
                    <input name="from_account" class="form-control" placeholder="از حساب">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary" type="submit">افزودن</button>
                </div>
            </form>
            <table class="table table-bordered" id="petty-cash-table">
                <thead>
                    <tr>
                        <th>عنوان خرج</th>
                        <th>مبلغ</th>
                        <th>تاریخ پرداخت</th>
                        <th>از حساب</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pettyCashes as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ number_format($item->amount) }}</td>
                            <td>{{ toJalali($item->paid_at)->format('Y-m-d') }}</td>
                            <td>{{ $item->from_account }}</td>
                            <td>
                                <a href="{{ route('simpleWorkflowReport.petty-cash.edit', $item) }}" class="btn btn-sm btn-primary">ویرایش</a>
                                <form action="{{ route('simpleWorkflowReport.petty-cash.destroy', $item) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('حذف شود؟')">حذف</button>
                                </form>
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
@endsection

@section('script')
<script>
    $('#petty-cash-table').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fa.json'
        },
        order: [[2, "desc"]],
        pageLength: 25,
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();

            // تبدیل مقادیر به عدد
            var intVal = function(i) {
                return typeof i === 'string'
                    ? i.replace(/[^\d\-\.]/g, '') * 1
                    : typeof i === 'number'
                        ? i
                        : 0;
            };

            // جمع کل (ستون مبلغ = index 1)
            var total = api
                .column(1, { search: 'applied' })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // جمع صفحه جاری
            var pageTotal = api
                .column(1, { page: 'current' })
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


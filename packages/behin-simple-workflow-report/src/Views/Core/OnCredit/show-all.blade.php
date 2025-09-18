@extends('behin-layouts.app')

@section('title', 'گزارش بدهکاران')


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
                            <td>{{ toJalali((string)$inbox->updated_at)->format('Y-m-d') }}</td>
                            <td>{{ $inbox->case_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right">جمع:</th>
                        <th></th> <!-- جمع ستون مبلغ اینجا میاد -->
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
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
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right">جمع:</th>
                        <th></th> <!-- اینجا مجموع مبلغ میاد -->
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
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
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // تابع برای حذف کاراکترهای غیرعددی (مثل ویرگول)
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,٬,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // جمع کل جدول
                var total = api
                    .column(2, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // جمع صفحه جاری
                var pageTotal = api
                    .column(2, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // نمایش در فوتر
                $(api.column(2).footer()).html(
                    'صفحه: ' + pageTotal.toLocaleString() + '<br>کل: ' + total.toLocaleString()
                );
            }
        });


        $('#on-credit-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // حذف جداکننده‌های هزارگان یا علامت‌های غیرعددی
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,٬,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // جمع کل جدول
                var total = api
                    .column(2, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // جمع صفحه جاری
                var pageTotal = api
                    .column(2, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // نمایش در فوتر
                $(api.column(2).footer()).html(
                    'صفحه: ' + pageTotal.toLocaleString() + '<br>کل: ' + total.toLocaleString()
                );
            }
        });
    </script>
@endsection

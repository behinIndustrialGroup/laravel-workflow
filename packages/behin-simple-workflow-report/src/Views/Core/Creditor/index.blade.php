@extends('behin-layouts.app')

@section('title', 'لیست طلبکاران')

@php
    use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
    $viewModelId = '7735ccdf-d5f2-4c38-8d02-ab7c139e5015';
    $viewModel = ViewModelController::getById($viewModelId);
    $viewModelUpdateForm = $viewModel->update_form;
    $viewModelApikey = $viewModel->api_key;
    $viewModelCreateNewForm = $viewModel->create_form;

    $addTasvieViewModelId = '6f34acb3-b60e-4a4a-99a5-4d3f8467ca6a';
    $addTasvieViewModel = ViewModelController::getById($addTasvieViewModelId);
    $addTasvieViewModelUpdateForm = $addTasvieViewModel->update_form;
    $addTasvieViewModelApikey = $addTasvieViewModel->api_key;
    $addTasvieViewModelCreateNewForm = $addTasvieViewModel->create_form;
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
            <button class="btn btn-sm btn-warning"
                onclick="open_view_model_create_new_form(`{{ $viewModelCreateNewForm }}`, `{{ $viewModelId }}`, `{{ $viewModelApikey }}`)">افزودن
                طلبکار</button>

            {{-- <button class="btn btn-sm btn-primary"
                onclick="open_view_model_create_new_form(`{{ $addTasvieViewModelCreateNewForm }}`, `{{ $addTasvieViewModelId }}`, `{{ $addTasvieViewModelApikey }}`)">افزودن
                سند</button> --}}
        </div>
    </div>
    <div class="card table-responsive">
        <div class="card-header bg-secondary text-center">
            <h3 class="card-title">گزارش لیست طلبکاران از شرکت</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="cheque-list">
                <thead>
                    <tr>
                        <th>طرف حساب</th>
                        <th>مانده حساب</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditors as $creditor)
                        <tr @if ($creditor->total_amount > 0) style="background: #f56c6c" @endif
                            @if ($creditor->total_amount < 0) class="bg-success" @endif>
                            <td>{{ $creditor->counterparty }}</td>
                            <td dir="ltr">{{ number_format($creditor->total_amount) }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    onclick="showDetails(`{{ $creditor->counterparty }}`)">جزئیات بیشتر</button>
                                <button class="btn btn-sm btn-success"
                                    onclick="showAddTasvie(`{{ $creditor->counterparty }}`)">افزودن سند پرداختی</button>
                                <button class="btn btn-sm btn-warning"
                                    onclick="showAddTalab(`{{ $creditor->counterparty }}`)">افزودن طلب</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th style="text-align:right">جمع:</th>
                        <th></th> <!-- اینجا جمع ستون مانده حساب -->
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#cheque-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // تابع برای حذف جداکننده هزارگان یا علامت‌های غیرعددی
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,٬,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // جمع کل جدول (ستون دوم = index 1)
                var total = api
                    .column(1, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // جمع صفحه جاری
                var pageTotal = api
                    .column(1, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // نمایش در فوتر
                $(api.column(1).footer()).html(
                    'صفحه: ' + pageTotal.toLocaleString() + '<br>کل: ' + total.toLocaleString()
                );
            }
        });


        function showDetails(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.creditor.show', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'جزئیات بیشتر');
        }

        function showAddTasvie(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.creditor.showAddTasvie', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'جزئیات بیشتر');
        }
        function showAddTalab(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.creditor.showAddTalab', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'جزئیات بیشتر');
        }
    </script>
@endsection

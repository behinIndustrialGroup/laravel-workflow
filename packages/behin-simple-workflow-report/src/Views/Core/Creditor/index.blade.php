@extends('behin-layouts.app')

@section('title', 'گزارش چک ها')

@php
    use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
    $viewModelId = '7735ccdf-d5f2-4c38-8d02-ab7c139e5015';
    $viewModel = ViewModelController::getById($viewModelId);
    $viewModelUpdateForm = $viewModel->update_form;
    $viewModelApikey = $viewModel->api_key;
    $viewModelCreateNewForm = $viewModel->create_form;
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
            <button class="btn btn-primary"
                onclick="open_view_model_create_new_form(`{{ $viewModelCreateNewForm }}`, `{{ $viewModelId }}`, `{{ $viewModelApikey }}`)">افزودن
                طلبکار</button>
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
                        <th>توضیحات</th>
                        <th>طرف حساب</th>
                        <th>مبلغ</th>
                        <th>شماره فاکتور</th>
                        <th>تاریخ فاکتور</th>
                        <th>وضعیت</th>
                        <th>تاریخ پرداخت</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditors as $creditor)
                        <tr>
                            <td>{{ $creditor->description }}</td>
                            <td>{{ $creditor->counterparty }}</td>
                            <td>{{ $creditor->amount }}</td>
                            <td>{{ $creditor->invoice_number }}</td>
                            <td>{{ $creditor->invoice_date }}</td>
                            <td>{{ $creditor->is_settled }}</td>
                            <td>{{ $creditor->settlement_date }}</td>
                            <td>
                                <button class="btn btn-primary"
                                    onclick="open_view_model_form(`{{ $viewModelUpdateForm }}`, `{{ $viewModelId }}`, `{{ $creditor->id }}`, `{{ $viewModelApikey }}`)">ویرایش</button>
                                <button class="btn btn-danger"
                                    onclick="delete_view_model_row(`{{ $viewModelId }}`, `{{ $creditor->id }}`, `{{ $viewModelApikey }}`)">حذف</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
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
            }
        });
    </script>
@endsection

@extends('behin-layouts.app')

@section('title', 'گزارش چک ها')

@php
    use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
    $viewModelId = '7735ccdf-d5f2-4c38-8d02-ab7c139e5015';
    $viewModel = ViewModelController::getById($viewModelId);
    $viewModelUpdateForm = $viewModel->update_form;
    $viewModelApikey = $viewModel->api_key;
    $viewModelCreateNewForm = $viewModel->create_form;

    $addTasvieViewModelId = "6f34acb3-b60e-4a4a-99a5-4d3f8467ca6a";
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

            <button class="btn btn-sm btn-primary"
                onclick="open_view_model_create_new_form(`{{ $addTasvieViewModelCreateNewForm }}`, `{{ $addTasvieViewModelId }}`, `{{ $addTasvieViewModelApikey }}`)">افزودن
                تسویه</button>
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
                        <th>مبلغ طلب</th>
                        {{-- <th>شماره فاکتور</th>
                        <th>تاریخ فاکتور</th>
                        <th>تسویه</th>
                        <th>نحوه تسویه</th>
                        <th>تاریخ پرداخت</th> --}}
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditors as $creditor)
                        <tr>
                            <td>{{ $creditor->description }}</td>
                            <td>{{ $creditor->counterparty }}</td>
                            <td dir="ltr">{{ number_format($creditor->total_amount) }}</td>
                            {{-- <td>{{ $creditor->invoice_number }}</td>
                            <td>{{ $creditor->invoice_date }}</td>
                            <td>{{ $creditor->is_settled }}</td>
                            <td>{{ $creditor->settlement_type }}</td>
                            <td>{{ $creditor->settlement_date }}</td> --}}
                            <td>
                                <button class="btn btn-primary"
                                    onclick="showDetails(`{{ $creditor->counterparty }}`)">جزئیات بیشتر</button>
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

        function showDetails(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.creditor.show', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'جزئیات بیشتر');
        }
    </script>
@endsection

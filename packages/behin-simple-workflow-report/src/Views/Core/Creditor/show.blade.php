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

<div class="card table-responsive">
    <div class="card-header bg-secondary text-center">
        <h3 class="card-title">جزئیات بیشتر طلبکار: {{ $creditors[0]->counterparty }}</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="more-details">
            <thead>
                <tr>
                    <th>نوع</th>
                    <th>طرف حساب</th>
                    <th>مبلغ طلب</th>
                    <th>شماره فاکتور</th>
                    <th>تاریخ فاکتور</th>
                    <th>توضیحات</th>
                    <th>اقدامات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($creditors as $creditor)
                    <tr>
                        <td>{{ $creditor->type }}</td>
                        <td>{{ $creditor->counterparty }}</td>
                        <td dir="ltr">{{ number_format($creditor->amount) }}</td>
                        <td>{{ $creditor->invoice_number }}</td>
                        <td>{{ $creditor->invoice_date }}</td>
                        <td>{{ $creditor->description }}</td>
                        <td>
                            @if ($creditor->type == 'طلب')
                                <button class="btn btn-sm btn-warning"
                                    onclick="open_view_model_form(`{{ $viewModelUpdateForm }}`, `{{ $viewModelId }}`, `{{ $creditor->id }}`, `{{ $viewModelApikey }}`)">ویرایش</button>
                            @endif
                            @if ($creditor->type == 'تسویه')
                                <button class="btn btn-sm btn-primary"
                                    onclick="open_view_model_form(`{{ $addTasvieViewModelUpdateForm }}`, `{{ $addTasvieViewModelId }}`, `{{ $creditor->id }}`, `{{ $addTasvieViewModelApikey }}`)">ویرایش</button>
                            @endif
                            <button class="btn btn-sm btn-danger"
                                onclick="delete_view_model_row(`{{ $viewModelId }}`, `{{ $creditor->id }}`, `{{ $viewModelApikey }}`)">حذف</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">مانده حساب:</th>
                    <th id="sum-amount"></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $('#more-details').DataTable({
        "pageLength": 25,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
        },
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // جمع کل در همین صفحه
            var pageTotal = api.column(2, {
                    page: 'current'
                }).data()
                .reduce(function(a, b) {
                    return parseInt(a.toString().replace(/,/g, '')) +
                        parseInt(b.toString().replace(/,/g, ''));
                }, 0);

            // جمع کل در کل جدول
            var total = api.column(2).data()
                .reduce(function(a, b) {
                    return parseInt(a.toString().replace(/,/g, '')) +
                        parseInt(b.toString().replace(/,/g, ''));
                }, 0);

            // نمایش در فوتر
            $('#sum-amount').html(
                total.toLocaleString() + ' (این صفحه: ' + pageTotal.toLocaleString() + ')'
            );
        }
    });
</script>

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
        <h3 class="card-title">جزئیات حساب: {{ $creditors[0]->counterparty()->name ?? '' }}</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="more-details">
            <thead>
                <tr>
                    <th>نوع</th>
                    <th>طرف حساب</th>
                    <th>مبلغ</th>
                    <th>بابت پرونده</th>
                    <th>نوع پرداختی</th>
                    <th>{{ trans('fields.invoice_or_cheque_number') }}</th>
                    <th>{{ trans('fields.transaction_or_cheque_due_date') }}</th>
                    <th>{{ trans('fields.destination_account_name') }}</th>
                    <th>{{ trans('fields.destination_account_number') }}</th>
                    <th>توضیحات</th>
                    <th>اقدامات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($creditors as $creditor)
                    <tr @if ($creditor->financial_type == 'بدهکار') style="background: #f56c6c" @endif
                        @if ($creditor->financial_type == 'بستانکار') class="bg-success" @endif>
                        <td>{{ $creditor->financial_type }}</td>
                        <td>{{ $creditor->counterparty()->name ?? '' }}</td>
                        <td dir="ltr">{{ number_format((int) $creditor->amount) }}</td>
                        <td>
                            @if (!empty($creditor->case_number))
                                <a href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $creditor->case_number]) }}"
                                    class="text-decoration-none me-1">
                                    <i class="fa fa-external-link text-primary"></i>
                                </a>
                            @endif
                            {{ $creditor->case_number }}</td>
                        <td>{{ $creditor->financial_method }}</td>
                        <td>{{ $creditor->invoice_or_cheque_number }}</td>
                        <td>{{ $creditor->transaction_or_cheque_due_date }}</td>
                        <td>{{ $creditor->destination_account_name }}</td>
                        <td>{{ $creditor->destination_account_number }}</td>
                        <td>{{ $creditor->description }}</td>
                        <td>
                            @if ($creditor->financial_type == 'بستانکار')
                                <button class="btn btn-sm btn-primary"
                                    onclick="showAddCredit(`{{ $creditor->counterparty_id }}`)">ویرایش</button>
                            @elseif ($creditor->financial_type == 'بدهکار')
                                <button class="btn btn-sm btn-primary"
                                    onclick="showAddDebit(`{{ $creditor->counterparty_id }}`)">ویرایش</button>
                            @endif
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
        "order": [
            [6, "desc"]
        ],
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // تابع کمکی برای محاسبه جمع با توجه به نوع تراکنش
            function calculateTotal(selector) {
                return api.rows(selector).data().reduce(function(a, b) {
                    var type = b[0]; // ستون اول: بدهکار یا بستانکار
                    var amount = parseInt(b[2].toString().replace(/,/g, '')) || 0;

                    // اگر بدهکار بود => منفی در نظر بگیر
                    if (type === 'بدهکار') amount = -amount;

                    return a + amount;
                }, 0);
            }

            // جمع کل در همین صفحه
            var pageTotal = calculateTotal({
                page: 'current'
            });

            // جمع کل در کل جدول
            var total = calculateTotal({});

            // نمایش در فوتر
            $('#sum-amount').html(
                total.toLocaleString() + ' (این صفحه: ' + pageTotal.toLocaleString() + ')'
            );
        }
    });
</script>

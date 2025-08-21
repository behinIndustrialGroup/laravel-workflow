<div class="container-fluid">
    <h5 class="mb-3">ثبت پرداخت برای پرونده {{ $onCredit->case_number }}</h5>
    <form method="POST" action="javascript:void(0);" id="payment-form">
        @csrf
        @method('PATCH')
        <table class="table" id="payment-rows">
            <thead>
                <tr>
                    <th>نوع پرداخت</th>
                    <th>جزئیات</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-secondary btn-sm" id="add-payment">افزودن ردیف پرداخت</button>

    </form>
    <div class="text-right mt-3">
        <button type="button" class="btn btn-primary" onclick="submitForm()">ذخیره</button>
    </div>

    @if ($payments->count())
        <hr>
        <h6 class="mb-2 p-2 bg-light text-center">پرداخت های ثبت شده</h6>
        <ul class="list-group">
            @foreach ($payments as $payment)
                @switch($payment->fix_cost_type)
                    @case('تسویه کامل - نقدی')
                        <li class="list-group-item">نقدی | {{ number_format($payment->payment) }} | {{ $payment->payment_date }} | {{ $payment->destination_account_name }} | {{ $payment->destination_account }}</li>
                    @break

                    @case('تسویه کامل - چک')
                        <li class="list-group-item">چک - {{ number_format($payment->payment) }} | {{ $payment->cheque_due_date }} | {{ $payment->cheque_number }} | {{ $payment->destination_account_name }} | {{ $payment->destination_account }}</li>
                    @break

                    @case('فاکتور')
                        <li class="list-group-item">فاکتور - {{ number_format($payment->payment) }} | {{ $payment->invoice_date }} | {{ $payment->invoice_number }} </li>
                    @break
                @endswitch
            @endforeach
        </ul>
    @endif
</div>
<form action="{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="is_passed" value="1">
    <button type="submit" class="btn btn-success">تسویه کامل</button>
</form>

<script>
    function submitForm() {
        var fd = new FormData(document.getElementById('payment-form'));
        var url = "{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}";
        send_ajax_formdata_request(url, fd, function(response) {
            alert('با موفقیت ذخیره شد.');
            open_admin_modal('{{ route('simpleWorkflowReport.on-credit-report.edit', $onCredit->id) }}', '', '{{ $onCredit->id }}')
        });
    }
    var rowIndex = 0;

    function addRow() {
        var row = `
            <tr>
                <td>
                    <select name="payments[${rowIndex}][type]" class="form-control payment-type">
                        <option value="تسویه کامل - نقدی">نقدی</option>
                        <option value="تسویه کامل - چک">چک</option>
                        <option value="فاکتور">فاکتور</option>
                    </select>
                </td>
                <td>
                    <div class="cash-fields payment-field-group">
                        <input type="text" name="payments[${rowIndex}][cash_amount]" class="form-control mb-1 formatted-digit" placeholder="مبلغ پرداختی">
                        <input type="text" name="payments[${rowIndex}][cash_date]" class="form-control mb-1 persian-date" placeholder="تاریخ پرداخت">
                        <input type="text" name="payments[${rowIndex}][account_number]" class="form-control mb-1" placeholder="شماره مقصد حساب">
                        <input type="text" name="payments[${rowIndex}][account_name]" class="form-control mb-1" placeholder="نام مقصد حساب">
                    </div>
                    <div class="cheque-fields payment-field-group d-none">
                        <input type="text" name="payments[${rowIndex}][cheque_amount]" class="form-control mb-1 formatted-digit" placeholder="مبلغ چک">
                        <input type="text" name="payments[${rowIndex}][cheque_date]" class="form-control mb-1 persian-date" placeholder="تاریخ سررسید چک">
                        <input type="text" name="payments[${rowIndex}][cheque_number]" class="form-control mb-1" placeholder="شماره چک">
                        <input type="text" name="payments[${rowIndex}][bank_name]" class="form-control mb-1" placeholder="نام بانک">
                    </div>
                    <div class="invoice-fields payment-field-group d-none">
                        <input type="text" name="payments[${rowIndex}][invoice_amount]" class="form-control mb-1 formatted-digit" placeholder="مبلغ فاکتور">
                        <input type="text" name="payments[${rowIndex}][invoice_date]" class="form-control mb-1 persian-date" placeholder="تاریخ فاکتور">
                        <input type="text" name="payments[${rowIndex}][invoice_number]" class="form-control mb-1" placeholder="شماره فاکتور">
                    </div>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
            </tr>`;
        var $row = $(row);
        $row.find('.payment-type').on('change', function() {
            var type = $(this).val();
            $row.find('.payment-field-group').addClass('d-none');
            if (type == 'تسویه کامل - نقدی') {
                $row.find('.cash-fields').removeClass('d-none');
            }
            if (type == 'تسویه کامل - چک') {
                $row.find('.cheque-fields').removeClass('d-none');
            }
            if (type == 'فاکتور') {
                $row.find('.invoice-fields').removeClass('d-none');
            }
        });
        $row.find('.remove-row').on('click', function() {
            $row.remove();
        });
        $('#payment-rows tbody').append($row);
        $('.persian-date').persianDatepicker({
            viewMode: 'day',
            initialValue: false,
            format: 'YYYY-MM-DD',
            initialValueType: 'persian'
        });
        rowIndex++;
        initial_view();
    }
    $('#add-payment').on('click', addRow);
    addRow();
</script>

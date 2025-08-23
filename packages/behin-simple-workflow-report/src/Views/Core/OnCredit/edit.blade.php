<div class="container-fluid py-3">
    <h5 class="mb-4 fw-bold text-primary">
        <i class="fa fa-credit-card"></i> ثبت پرداخت برای پرونده {{ $onCredit->case_number }}
    </h5>

    <form method="POST" action="javascript:void(0);" id="payment-form">
        @csrf
        @method('PATCH')

        <div id="payment-rows" class="row gy-3"></div>

        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="add-payment">
                <i class="fa fa-plus"></i> افزودن ردیف پرداخت
            </button>
            <button type="button" class="btn btn-success rounded-pill px-4" onclick="submitForm()">
                <i class="fa fa-save"></i> ذخیره
            </button>
        </div>
    </form>
    <div id="payments-list">
        @include('SimpleWorkflowReportView::Core.OnCredit.payments-list', ['payments' => $payments])
    </div>

    <form class="mt-4" action="{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}"
        method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="is_passed" value="1">
        <button type="submit" class="btn btn-danger w-100 rounded-pill">
            <i class="fa fa-check-circle"></i> تسویه کامل
        </button>
    </form>
</div>

<script>
    function submitForm() {
        var fd = new FormData(document.getElementById('payment-form'));
        var url = "{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}";
        send_ajax_formdata_request(url, fd, function(response) {
            console.log(response);
            if (response.status === 'success') {
                alert(response.message);
            } else {
                alert(response.message);
            }
        });
    }

    var rowIndex = 0;

    function addRow() {
        var row = `
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">نوع پرداخت</label>
                                <select name="payments[${rowIndex}][type]" class="form-select payment-type">
                                    <option value="نقدی">نقدی</option>
                                    <option value="چک">چک</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="cash-fields payment-field-group">
                                    <div class="form-floating mb-2">
                                        <label>مبلغ پرداختی</label>
                                        <input type="text" class="form-control formatted-digit" name="payments[${rowIndex}][cash_amount]" placeholder="مبلغ">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>تاریخ پرداخت</label>
                                        <input type="text" class="form-control persian-date" name="payments[${rowIndex}][cash_date]" placeholder="تاریخ">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>شماره حساب مقصد</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][account_number]" placeholder="شماره حساب">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>نام حساب مقصد</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][account_name]" placeholder="نام حساب">
                                    </div>
                                    <div class="form-floating">
                                        <label>شماره فاکتور</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][cash_invoice_number]" placeholder="فاکتور">
                                    </div>
                                </div>
                                <div class="cheque-fields payment-field-group d-none">
                                    <div class="form-floating mb-2">
                                        <label>مبلغ چک</label>
                                        <input type="text" class="form-control formatted-digit" name="payments[${rowIndex}][cheque_amount]" placeholder="مبلغ چک">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>تاریخ سررسید</label>
                                        <input type="text" class="form-control persian-date" name="payments[${rowIndex}][cheque_due_date]" placeholder="سررسید">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>شماره چک</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][cheque_number]" placeholder="شماره چک">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>نام بانک</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][bank_name]" placeholder="نام بانک">
                                    </div>
                                    <div class="form-floating mb-2">
                                        <label>در وجه</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][account_name]" placeholder="نام حساب مقصد">
                                    </div>
                                    <div class="form-floating">
                                        <label>شماره فاکتور</label>
                                        <input type="text" class="form-control" name="payments[${rowIndex}][cheque_invoice_number]" placeholder="فاکتور">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-row">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        const $row = $(row);
        $row.find('.payment-type').on('change', function() {
            const type = $(this).val();
            $row.find('.payment-field-group').addClass('d-none');
            if (type === 'نقدی') {
                $row.find('.cash-fields').removeClass('d-none');
            }
            if (type === 'چک') {
                $row.find('.cheque-fields').removeClass('d-none');
            }
        });
        $row.find('.remove-row').on('click', function() {
            $row.remove();
        });
        $('#payment-rows').append($row);
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

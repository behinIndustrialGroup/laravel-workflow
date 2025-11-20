<h4>ویرایش تراکنش مالی</h4>
<form action="{{ route('simpleWorkflowReport.financial-transactions.update', $financialTransaction) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row col-sm-12 p-0 m-0 dynamic-form" id="dfd41076-26ca-47e4-ab34-17bec3bd89db">
        <div class="col-sm-12">
            <div class="form-group">
                <label>توضیحات</label>
                <input type="text" name="description" list="description_list" value="{{ $financialTransaction->description }}"
                    class="form-control" id="description" placeholder="" style="">
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>نوع تراکنش</label>
                <select name="financial_type" class="form-control select2" id="financial_type">
                    <option value="">انتخاب کنید</option>
                    <option value="بستانکار" @selected($financialTransaction->financial_type === 'بستانکار')>بستانکار</option>
                    <option value="بدهکار" @selected($financialTransaction->financial_type === 'بدهکار')>بدهکار</option>
                </select>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>طرف حساب</label>
                <select name="counterparty_id" class="form-control select2" id="counterparty">
                    <option value="">انتخاب کنید</option>
                    @foreach ($counterParties as $counterParty)
                        <option value="{{ $counterParty->id }}" @selected($financialTransaction->counterparty_id == $counterParty->id)>
                            {{ $counterParty->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-8"></div>
        <div class="col-sm-4">
            <div class="form-group"><label>بابت پرونده</label>
                <input type="text" name="case_number" list="case_number_list"
                    class="form-control formatted-digit" inputmode="numeric" id="case_number" placeholder=""
                    style="" value="{{ $financialTransaction->case_number }}">
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>مبلغ</label><input type="text" name="amount" list="amount_list"
                    class="form-control formatted-digit" inputmode="numeric" id="amount" placeholder=""
                    style="" value="{{ number_format((int) $financialTransaction->amount) }}"></div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>{{ trans('fields.financial_method') }}</label>
                <select name="financial_method" class="form-control select2" id="financial_method">
                    <option value="">انتخاب کنید</option>
                    <option value="نقدی" @selected($financialTransaction->financial_method === 'نقدی')>نقدی</option>
                    <option value="چک" @selected($financialTransaction->financial_method === 'چک')>چک</option>
                </select>
            </div>

        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>{{ trans('fields.invoice_or_cheque_number') }}</label><input type="text"
                    name="invoice_or_cheque_number" list="invoice_or_cheque_number_list" value="{{ $financialTransaction->invoice_or_cheque_number }}"
                    class="form-control" id="invoice_or_cheque_number" placeholder="" style=""></div>

        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>{{ trans('fields.transaction_or_cheque_due_date') }}</label>
                <input type="text" name="transaction_or_cheque_due_date" value="{{ $financialTransaction->transaction_or_cheque_due_date }}"
                    class="form-control pwt-datepicker-input-element" id="transaction_or_cheque_due_date" placeholder=""
                    style="" script="">
                <input type="hidden" name="transaction_or_cheque_due_date_alt" id="transaction_or_cheque_due_date_alt"
                    value="{{ $financialTransaction->transaction_or_cheque_due_date_alt }}">
                <script>
                    $('#transaction_or_cheque_due_date').persianDatepicker({
                        viewMode: 'day',
                        initialValue: false,
                        format: 'YYYY-MM-DD',
                        initialValueType: 'persian',
                        altField: '#transaction_or_cheque_due_date_alt',
                        calendar: {
                            persian: {
                                leapYearMode: 'astronomical',
                                locale: 'fa'
                            }
                        }
                    });
                </script>
            </div>

        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <div class="form-group"><label>نام مقصد حساب</label><input type="text" name="destination_account_name"
                    list="destination_account_name_list" value="{{ $financialTransaction->destination_account_name }}"
                    class="form-control" id="destination_account_name" placeholder="" style=""></div>

        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>شماره مقصد حساب</label><input type="text"
                    name="destination_account_number" list="destination_account_number_list" value="{{ $financialTransaction->destination_account_number }}"
                    class="form-control" id="destination_account_number" placeholder="" style=""></div>

        </div>

    </div>
    <input type="submit" value="ذخیره" class="btn btn-primary m-2">
</form>

<script>
    initial_view();
</script>

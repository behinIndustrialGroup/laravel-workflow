<h4>فرم افزودن بستانکاری</h4>
<form action="{{ route('simpleWorkflowReport.financial-transactions.addCredit') }}" method="POST">
    @csrf
    <div class="row col-sm-12 p-0 m-0 dynamic-form" id="dfd41076-26ca-47e4-ab34-17bec3bd89db">
        <div class="col-sm-12">
            <div class="form-group"><label>توضیحات</label><input type="text" name="description" list="description_list"
                    value="" class="form-control" id="description" placeholder="" style="">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group"><label>طرف حساب</label>
                @if (isset($counterparty))
                    <input type="text" name="counterparty_name" value="{{ $counterparty->name }}" class="form-control"
                        id="counterparty_name" readonly>
                    <input type="hidden" name="counterparty_id" value="{{ $counterparty->id }}" class="form-control"
                        id="counterparty" readonly>
                @else
                    <select name="counterparty_id" class="form-control select2" id="counterparty">
                        <option value="">انتخاب کنید</option>
                        @foreach ($counterParties as $counterParty)
                            <option value="{{ $counterParty->id }}">{{ $counterParty->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        <div class="col-sm-8"></div>



        <div class="col-sm-4">
            <div class="form-group">
                <label>بابت پرونده</label>
                <input type="text" name="case_number" list="case_number_list" class="form-control formatted-digit"
                    inputmode="numeric" id="case_number" placeholder="" style="">
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>مبلغ</label><input type="text" name="amount" list="amount_list"
                    class="form-control formatted-digit" inputmode="numeric" id="amount" placeholder=""
                    style=""></div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>{{ trans('fields.financial_method') }}</label>
                <select name="financial_method" class="form-control select2" id="financial_method">
                    <option value="">انتخاب کنید</option>
                    <option value="نقدی">نقدی</option>
                    <option value="چک">چک</option>
                </select>
            </div>

        </div>



        <div class="col-sm-4">
            <div class="form-group"><label>{{ trans('fields.invoice_or_cheque_number') }}</label><input type="text"
                    name="invoice_or_cheque_number" list="invoice_or_cheque_number_list" value=""
                    class="form-control" id="invoice_or_cheque_number" placeholder="" style=""></div>

        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>{{ trans('fields.transaction_or_cheque_due_date') }}</label>
                <input type="text" name="transaction_or_cheque_due_date" value=""
                    class="form-control pwt-datepicker-input-element" id="transaction_or_cheque_due_date" placeholder=""
                    style="" script="">
                <input type="hidden" name="transaction_or_cheque_due_date_alt" id="transaction_or_cheque_due_date_alt">
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
                    list="destination_account_name_list" value="" class="form-control"
                    id="destination_account_name" placeholder="" style=""></div>

        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>شماره مقصد حساب</label><input type="text"
                    name="destination_account_number" list="destination_account_number_list" value=""
                    class="form-control" id="destination_account_number" placeholder="" style=""></div>

        </div>
    </div>
    <input type="submit" value="ذخیره" class="btn btn-primary m-2">
</form>


<script>
    initial_view()
</script>

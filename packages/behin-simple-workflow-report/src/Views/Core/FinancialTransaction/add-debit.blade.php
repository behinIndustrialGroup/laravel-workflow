<h4>فرم افزودن بدهکاری</h4>
<form action="{{ route('simpleWorkflowReport.financial-transactions.addDebit') }}" method="POST">
    @csrf
    <div class="row col-sm-12 p-0 m-0 dynamic-form" id="dfd41076-26ca-47e4-ab34-17bec3bd89db">
        <div class="col-sm-12">
            <div class="form-group"><label>توضیحات</label><input type="text" name="description" list="description_list"
                    value="" class="form-control" id="description" placeholder="" style="">
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                @if (isset($counterparty))
                    <label>طرف حساب</label>
                    <input type="text" name="counterparty_name" value="{{ $counterparty->name }}"
                        class="form-control" id="counterparty_name" readonly>
                    <input type="hidden" name="counterparty_id" value="{{ $counterparty->id }}" class="form-control"
                        id="counterparty" readonly>
                @else
                    @php
                        $fieldName = 'counterparty_id';
                        $fieldDetails = getFieldDetailsByName($fieldName);
                        $fieldValue = null;
                        $fieldValueAlt = null;
                    @endphp
                    <div class="">
                        @include('SimpleWorkflowView::Core.Form.field-generator', [
                            'fieldName' => $fieldName,
                            'fieldId' => $fieldName,
                            'fieldClass' => 'col-sm-12',
                            'readOnly' => true,
                            'required' => false,
                            'fieldValue' => $fieldValue,
                            'fieldValueAlt' => $fieldValueAlt ?? '',
                        ])
                    </div>
                    {{-- <select name="counterparty_id" class="form-control select2" id="counterparty">
                        <option value="">انتخاب کنید</option>
                        @foreach ($counterParties as $counterParty)
                            <option value="{{ $counterParty->id }}">{{ $counterParty->name }}</option>
                        @endforeach
                    </select> --}}
                @endif
            </div>
        </div>
        <div class="col-sm-8"></div>
        <div class="col-sm-4">
            <div class="form-group"><label>بابت پرونده</label>
                <input type="text" name="case_number" list="case_number_list" class="form-control"
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
        {{-- <div class="col-sm-4">
            <div class="form-group"><label>نام مقصد حساب</label><input type="text" name="destination_account_name"
                    list="destination_account_name_list" value="" class="form-control"
                    id="destination_account_name" placeholder="" style="">
                <datalist id="destination_account_name_list"></datalist>
            </div>

        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>شماره مقصد حساب</label><input type="text"
                    name="destination_account_number" list="destination_account_number_list" value=""
                    class="form-control" id="destination_account_number" placeholder="" style=""></div>

        </div> --}}


    </div>
    <input type="submit" value="ذخیره" class="btn btn-primary m-2">
</form>


<script>
    initial_view()
</script>

<script>
    let counterPartyDataMap = {};

    function getCounterParty(q, input_id) {
        var scriptId = "0fa291ce-6b0a-4e0b-b9aa-e6b65337f97c";
        var fd = new FormData();
        fd.append('q', q);
        runScript(scriptId, fd, function(response) {
            console.log(response);
            var list = $(`#${input_id}_list`);
            counterPartyDataMap = {}; // ریست آبجکت

            if (list.length) {
                list.html('');
                response.forEach(function(item) {
                    counterPartyDataMap[item.name] = item; // ذخیره اطلاعات هر مشتری بر اساس fullname
                    list.append(`<option value="${item.name}"></option>`);
                });
            } else {
                $('#account_number').after(`<datalist id="${input_id}_list"></datalist>`);
                list = $(`#${input_id}_list`);
                response.forEach(function(item) {
                    counterPartyDataMap[item.name] = item; // ذخیره اطلاعات هر مشتری بر اساس fullname
                    list.append(`<option value="${item.name}"></option>`);
                });
            }
        });
    }

    $('#destination_account_name').on('input', function() {
        var q = $(this).val();
        var selected = counterPartyDataMap[q];
        if (selected) {
            $('#destination_account_number').val(selected.account_number || '');
        }
    });

    $('#destination_account_name').keyup(function() {
        if ($(this).val().length >= 3) {
            getCounterParty($(this).val(), $(this).attr('id'));
        }
    });
</script>

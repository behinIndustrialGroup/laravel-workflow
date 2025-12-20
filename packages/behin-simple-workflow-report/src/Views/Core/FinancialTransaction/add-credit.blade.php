<h4>فرم افزودن بستانکاری</h4>
<form action="javascript:void(0)" method="POST" id="add-credit-form">
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
                    <input type="text" name="counterparty_name" value="{{ $counterparty->name }}"
                        class="form-control" id="counterparty_name" readonly>
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
        <div class="col-sm-4">
            <label for="">در ریز خرج کرد ثبت شود؟</label>
            <select name="store_in_pretty_cash" id="store_in_pretty_cash" class="form-control">
                <option value="0">خیر</option>
                <option value="1">بله</option>
            </select>
        </div>
        <div class="col-sm-4">
            <label for="">طرف حساب مقصد دارد؟</label>
            <select name="has_destination_account" id="has_destination_account" class="form-control">
                <option value="1">بله</option>
                <option value="0">خیر</option>
            </select>
        </div>
        <div class="col-sm-4">
            <label>طرف حساب مقصد</label>
            <select name="destination_account_id" class="form-control select2" id="destination_account_id">
                <option value="">انتخاب کنید</option>
                @foreach ($counterParties as $counterParty)
                    <option value="{{ $counterParty->id }}">{{ $counterParty->name }}</option>
                @endforeach
            </select>
        </div>
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
</form>
<div class="row">
    <button class="btn btn-primary" onclick="submitForm()">
        ذخیره
    </button>
</div>


<script>
    initial_view()
</script>

<script>
    let counterPartyDataMap = {};

    function submitForm(){
        var fd = new FormData($('#add-credit-form')[0]);
        var url = "{{ route('simpleWorkflowReport.financial-transactions.addCredit') }}";
        send_ajax_formdata_request(
            url,
            fd,
            function(res){
                console.log(res);
                show_message("ذخیره شد");
                window.location.reload();
            },
            function(res){
                console.log(res)
                show_error(res);
            }
        )
    }

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

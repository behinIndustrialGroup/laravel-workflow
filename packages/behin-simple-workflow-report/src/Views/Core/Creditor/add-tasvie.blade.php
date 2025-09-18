<form action="{{ route('simpleWorkflowReport.creditor.addTasvie' ) }}" method="POST">
    @csrf
    <div class="row col-sm-12 p-0 m-0 dynamic-form" id="dfd41076-26ca-47e4-ab34-17bec3bd89db">
        <div class="col-sm-12">
            <div class="form-group"><label>توضیحات</label><input type="text" name="description"
                    list="description_list" value="" class="form-control" id="description" placeholder=""
                    style="">
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>طرف حساب</label>
                <input type="text" name="counterparty" value="{{ $counterparty }}" class="form-control"
                    id="counterparty" readonly>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>مبلغ</label><input type="text" name="amount" list="amount_list"
                    class="form-control formatted-digit" inputmode="numeric"
                    id="amount" placeholder="" style=""></div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>تاریخ تسویه / سررسید چک</label>
                <input type="text" name="invoice_date" value=""
                    class="form-control pwt-datepicker-input-element" id="invoice_date" placeholder="" style=""
                    script="">
                <input type="hidden" name="invoice_date_alt" id="invoice_date_alt">
                <script>
                    $('#invoice_date').persianDatepicker({
                        viewMode: 'day',
                        initialValue: false,
                        format: 'YYYY-MM-DD',
                        initialValueType: 'persian',
                        altField: '#invoice_date_alt',
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

        <div class="col-sm-4">
            <div class="form-group"><label>شماره چک</label><input type="text" name="invoice_number"
                    list="invoice_number_list" value="" class="form-control" id="invoice_number" placeholder=""
                    style=""></div>

        </div>

        <div class="col-sm-4">
            <div class="form-group"><label>نحوه تسویه</label>
                <select name="settlement_type"
                    class="form-control select2"
                    id="settlement_type">
                    <option value="">انتخاب کنید</option>
                    <option value="نقدی">نقدی</option>
                    <option value="چک">چک</option>
                </select>
            </div>

        </div>
    </div>
    <input type="submit" value="ذخیره" class="btn btn-primary m-2">
</form>


<script>
    
    initial_view()
</script>

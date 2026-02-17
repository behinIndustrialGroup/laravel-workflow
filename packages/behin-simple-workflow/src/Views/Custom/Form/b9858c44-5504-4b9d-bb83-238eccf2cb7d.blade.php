@php
    use Behin\SimpleWorkflow\Models\Entities\Consumable_parts;

    if (isset($row)) {
        $consumablePart = Consumable_parts::find($row->id);
    }
@endphp
@if (isset($consumablePart))
    @if ($consumablePart->consumable_part_status == 'در انتظار تحویل انبار')
        <div class="row col-sm-12 p-0 m-0 dynamic-form" id="b9858c44-5504-4b9d-bb83-238eccf2cb7d">

            <div class="col-sm-4">
                <div class="form-group">
                    <label>نام محصول <span class="text-danger">*</span></label><input type="text" name="product_name"
                        list="product_name_list" value="{{ $consumablePart->product_name }}" class="form-control"
                        id="product_name" placeholder="" required="" style=""><datalist
                        id="product_name_list"></datalist>
                </div>

            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label>تعداد موردنیاز <span class="text-danger">*</span></label><input type="text"
                        name="requested_quantity" list="requested_quantity_list" class="form-control formatted-digit"
                        inputmode="numeric" pattern="[0-9]*" value="{{ $consumablePart->requested_quantity }}"
                        id="requested_quantity" placeholder="" required="" style="">
                </div>
            </div>
        </div>
    @elseif($consumablePart->consumable_part_status == 'تایید')
        <div class="row col-sm-12 p-0 m-0 dynamic-form" id="b9858c44-5504-4b9d-bb83-238eccf2cb7d">

            <div class="col-sm-4">
                <div class="form-group">
                    <label>نام محصول <span class="text-danger">*</span></label><input type="text" name="product_name"
                        list="product_name_list" value="{{ $consumablePart->product_name }}" readonly
                        class="form-control" id="product_name" placeholder="" required="" style=""><datalist
                        id="product_name_list"></datalist>
                </div>

            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label>تعداد موردنیاز <span class="text-danger">*</span></label><input type="text"
                        name="requested_quantity" list="requested_quantity_list" readonly
                        class="form-control formatted-digit" inputmode="numeric" pattern="[0-9]*"
                        value="{{ $consumablePart->requested_quantity }}" id="requested_quantity" placeholder=""
                        required="" style="">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>تعداد بازگشت داده شده<span class="text-danger">*</span></label><input type="text"
                        name="returned_quantity" list="returned_quantity" class="form-control formatted-digit"
                        inputmode="numeric" pattern="[0-9]*" value="{{ $consumablePart->returned_quantity }}"
                        id="returned_quantity" placeholder="" required="" style="">
                </div>
            </div>
        </div>
    @endif
@else
    <div class="row col-sm-12 p-0 m-0 dynamic-form" id="b9858c44-5504-4b9d-bb83-238eccf2cb7d">

        <div class="col-sm-4">
            <div class="form-group">
                <label>نام محصول <span class="text-danger">*</span></label><input type="text" name="product_name"
                    list="product_name_list" value="" class="form-control" id="product_name" placeholder=""
                    required="" style=""><datalist id="product_name_list"></datalist>
            </div>

        </div>

        <div class="col-sm-2">
            <div class="form-group">
                <label>تعداد موردنیاز <span class="text-danger">*</span></label><input type="text"
                    name="requested_quantity" list="requested_quantity_list" class="form-control formatted-digit"
                    inputmode="numeric" pattern="[0-9]*" value="" id="requested_quantity" placeholder=""
                    required="" style="">
            </div>
        </div>
    </div>
@endif
<button class="btn btn-sm btn-success view-model-update-btn"
                    onclick="updateViewModelRecord(`{{ $row->id ?? '' }}`)">{{ trans('fields.Save') }}</button>

                <script>
                    function updateViewModelRecord(row_id) {
        var fd = new FormData($(`#modal-form-${row_id}`)[0]);
        var url = "{{ route('simpleWorkflow.view-model.update-record') }}"
        send_ajax_formdata_request(url, fd, function(response) {
            show_message(response)
            console.log(response)
            get_view_model_rows('{{ $viewModel->id }}', '{{ $viewModel->api_key }}')
            close_admin_modal()
        })
    }
                </script>

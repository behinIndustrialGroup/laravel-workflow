@extends('behin-layouts.app')

@section('title', 'درخواست قطعات مصرفی')

@section('content')
    <style>
        td,
        th {
            white-space: nowrap;
        }
    </style>
    @if (access('مدیریت انبار قطعات مصرفی'))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">درخواست قطعات مصرفی</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>تاریخ درخواست</th>
                            <th>شماره پرونده</th>
                            <th>نام مشتری</th>
                            <th>درخواست کننده</th>
                            <th>قطعه</th>
                            <th>تعداد مورد نیاز</th>
                            <th>تعداد تحویل شده</th>
                            <th>تعداد بازگشت شده به انبار</th>
                            <th>منبع تامین</th>
                            <th>شماره فاکتور</th>
                            <th>تاریخ فاکتور</th>
                            <th style="width: 250px !important">مبلغ</th>
                            <th style="width: 250px !important">وضعیت</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumableParts->whereIn('consumable_part_status', ['در انتظار تحویل انبار', 'در انتظار تایید بازگشتی ها توسط انبار']) as $consumablePart)
                            <form action="{{ route('simpleWorkflowReport.consumable-parts.deliver', $consumablePart->id) }}"
                                method="post">
                                @csrf
                                @method('PUT')
                                <tr>
                                    <td dir="ltr">{{ toJalali($consumablePart->created_at) }}</td>
                                    <td>{{ $consumablePart->case_number ?? '' }}</td>
                                    <td>{{ $consumablePart->case()?->customerName() ?? '' }}</td>
                                    <td>{{ $consumablePart->created_by ? getUserInfo($consumablePart->created_by)?->name : '' }}
                                    </td>
                                    <td>{{ $consumablePart->product_name }}</td>
                                    <td>{{ $consumablePart->requested_quantity }}</td>
                                    <td>
                                        @if ($consumablePart->consumable_part_status == 'در انتظار تحویل انبار')
                                            <input type="number" name="delivered_quantity" id=""
                                                value="{{ $consumablePart->delivered_quantity }}" class="form-control">
                                        @else
                                            {{ $consumablePart->delivered_quantity }}
                                        @endif
                                    </td>
                                    <td>{{ $consumablePart->returned_quantity }}</td>
                                    @if ($consumablePart->external_buying_is_done == 1)
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @else
                                        <td>
                                            <input type="text" name="supply_source" id="supply_source"
                                                value="{{ $consumablePart->supply_source }}" class="form-control"
                                                style="width: 150px">
                                        </td>
                                        <td>
                                            <input type="text" name="invoice_number" id="invoice_number"
                                                value="{{ $consumablePart->invoice_number }}" class="form-control"
                                                style="width: 150px">
                                        </td>
                                        <td>
                                            @include('SimpleWorkflowView::Core.Form.field-generator', [
                                                'fieldName' => 'invoice_date',
                                                'fieldId' => 'invoice_date_' . $consumablePart->id,
                                                'fieldClass' => 'col-sm-12 text-center',
                                                'readOnly' => false,
                                                'required' => false,
                                                'showLabel' => false,
                                                'fieldValue' => $consumablePart->invoice_date,
                                                'fieldValueAlt' => $consumablePart->invoice_date_alt,
                                            ])
                                        </td>
                                        <td>
                                            <input type="text" name="amount" id="amount"
                                                value="{{ $consumablePart->amount }}" dir="ltr" class="form-control"
                                                style="width: 150px">
                                        </td>
                                    @endif
                                    <td style="width: 250px">
                                        <select name="consumable_part_status" id=""
                                            class="form-control consumable-status" data-id="{{ $consumablePart->id }}"
                                            style="width: 250px">
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status['label'] }}"
                                                    {{ $status['label'] == $consumablePart->consumable_part_status ? 'selected' : '' }}>
                                                    {{ $status['label'] }}</option>
                                            @endforeach
                                        </select>
                                        {{-- فیلد تعداد خرید --}}
                                        <div class="purchase-quantity-box mt-2" id="purchase-box-{{ $consumablePart->id }}"
                                            style="display:none;">

                                            <div class="alert alert-warning p-2 mb-2">
                                                چندتا از این بخریم؟
                                            </div>

                                            <input type="number" name="required_quantity_to_purchase" class="form-control"
                                                placeholder="تعداد مورد نیاز برای خرید">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="submit" name="" id="" class="btn btn-sm btn-primary"
                                            value="ذخیره">
                                    </td>
                                </tr>
                            </form>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if (access('مدیریت لیست خرید داخلی'))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">لیست خرید داخلی</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>تاریخ درخواست</th>
                            <th>شماره پرونده</th>
                            <th>نام مشتری</th>
                            <th>قطعه</th>
                            <th>تعداد مورد نیاز برای خرید</th>
                            <th>تعداد خریداری شده</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumableParts->whereIn('consumable_part_status', ['در انتظار خرید داخلی']) as $consumablePart)
                            <form
                                action="{{ route('simpleWorkflowReport.consumable-parts.deliver', $consumablePart->id) }}"
                                method="post">
                                @csrf
                                @method('PUT')
                                <tr>
                                    <td dir="ltr">{{ toJalali($consumablePart->created_at) }}</td>
                                    <td>{{ $consumablePart->case_number }}</td>
                                    <td>{{ $consumablePart->case()?->customerName() ?? '' }}</td>
                                    <td>{{ $consumablePart->product_name }}</td>
                                    <td>{{ $consumablePart->required_quantity_to_purchase }}</td>
                                    {{-- <td>
                                        <input type="number" name="required_quantity_to_purchase" id=""
                                            value="{{ $consumablePart->required_quantity_to_purchase }}"
                                            class="form-control">
                                    </td> --}}
                                    <td>
                                        <input type="number" name="purchased_quantity" id=""
                                            value="{{ $consumablePart->purchased_quantity }}" class="form-control">
                                    </td>
                                    <td style="width: 250px">
                                        <select name="consumable_part_status" id="" class="form-control"
                                            style="width: 250px">
                                            @foreach (collect($statuses)->whereIn('key', ['pending_buy_in', 'pending_buy_out', 'requested']) as $status)
                                                <option value="{{ $status['label'] }}"
                                                    {{ $status['label'] == $consumablePart->consumable_part_status ? 'selected' : '' }}>
                                                    {{ $status['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" name="" id="" class="btn btn-sm btn-primary"
                                            value="ذخیره">
                                    </td>
                                </tr>
                            </form>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if (access('مدیریت لیست خرید خارجی'))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">لیست خرید خارجی</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>تاریخ درخواست</th>
                            <th>شماره پرونده</th>
                            <th>نام مشتری</th>
                            <th>قطعه</th>
                            <th>تعداد مورد نیاز برای خرید</th>
                            <th>منبع تامین</th>
                            <th>شماره فاکتور</th>
                            <th>تاریخ فاکتور</th>
                            <th style="width: 250px !important">مبلغ</th>
                            <th>تعداد خریداری شده</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumableParts->whereIn('consumable_part_status', ['در انتظار خرید خارجی']) as $consumablePart)
                            <form
                                action="{{ route('simpleWorkflowReport.consumable-parts.store-external-buying-info', $consumablePart->id) }}"
                                method="post">
                                @csrf
                                @method('PUT')
                                <tr>
                                    <td dir="ltr">{{ toJalali($consumablePart->created_at) }}</td>
                                    <td>{{ $consumablePart->case_number }}</td>
                                    <td>{{ $consumablePart->case()?->customerName() ?? '' }}</td>
                                    <td>{{ $consumablePart->product_name }}</td>
                                    <td>{{ $consumablePart->required_quantity_to_purchase }}</td>
                                    {{-- <td>
                                        <input type="number" name="required_quantity_to_purchase" id=""
                                            value="{{ $consumablePart->required_quantity_to_purchase }}"
                                            class="form-control">
                                    </td> --}}
                                    <td>
                                        <input type="text" name="supply_source" id="supply_source"
                                            value="{{ $consumablePart->supply_source }}" class="form-control"
                                            style="width: 150px">
                                    </td>
                                    <td>
                                        <input type="text" name="invoice_number" id="invoice_number"
                                            value="{{ $consumablePart->invoice_number }}" class="form-control"
                                            style="width: 150px">
                                    </td>
                                    <td>
                                        @include('SimpleWorkflowView::Core.Form.field-generator', [
                                            'fieldName' => 'invoice_date',
                                            'fieldId' => 'invoice_date_' . $consumablePart->id,
                                            'fieldClass' => 'col-sm-12 text-center',
                                            'readOnly' => false,
                                            'required' => false,
                                            'showLabel' => false,
                                            'fieldValue' => $consumablePart->invoice_date,
                                            'fieldValueAlt' => $consumablePart->invoice_date_alt,
                                        ])
                                    </td>
                                    <td style="width:100px">
                                        <input type="text" name="amount" id="amount"
                                                value="{{ $consumablePart->amount }}" dir="ltr" class="form-control formatted-digit"
                                                style="width: 150px">
                                        
                                    </td>
                                    <td>
                                        <input type="number" name="purchased_quantity" id=""
                                            value="{{ $consumablePart->purchased_quantity }}" class="form-control">
                                    </td>
                                    <td style="width: 250px">
                                        <select name="consumable_part_status" id="" class="form-control"
                                            style="width: 250px">
                                            @foreach (collect($statuses)->whereIn('key', ['pending_buy_in', 'pending_buy_out', 'requested']) as $status)
                                                <option value="{{ $status['label'] }}"
                                                    {{ $status['label'] == $consumablePart->consumable_part_status ? 'selected' : '' }}>
                                                    {{ $status['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" name="" id=""
                                            class="btn btn-sm btn-primary" value="ذخیره">
                                    </td>
                                </tr>
                            </form>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const selects = document.querySelectorAll('.consumable-status');

            selects.forEach(function(select) {

                function togglePurchaseField() {

                    const selectedValue = select.value;
                    const id = select.dataset.id;

                    const box = document.getElementById('purchase-box-' + id);

                    if (
                        selectedValue === 'در انتظار خرید داخلی' ||
                        selectedValue === 'در انتظار خرید خارجی'
                    ) {
                        box.style.display = 'block';
                    } else {
                        box.style.display = 'none';
                    }
                }

                // بار اول
                togglePurchaseField();

                // هنگام تغییر
                select.addEventListener('change', togglePurchaseField);
            });

        });
    </script>
@endsection

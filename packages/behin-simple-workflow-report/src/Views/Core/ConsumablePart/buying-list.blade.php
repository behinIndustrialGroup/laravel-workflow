@extends('behin-layouts.app')

@section('title', 'درخواست قطعات مصرفی')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
                        <form action="{{ route('simpleWorkflowReport.consumable-parts.deliver', $consumablePart->id) }}"
                            method="post">
                            @csrf
                            @method('PUT')
                            <tr>
                                <td dir="ltr">{{ toJalali($consumablePart->created_at) }}</td>
                                <td>{{ $consumablePart->case_number }}</td>
                                <td>{{ $consumablePart->case()->customerName() }}</td>
                                <td>{{ $consumablePart->product_name }}</td>
                                <td>
                                    <input type="number" name="required_quantity_to_purchase" id=""
                                        value="{{ $consumablePart->required_quantity_to_purchase }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="purchased_quantity" id=""
                                        value="{{ $consumablePart->purchased_quantity }}" class="form-control">
                                </td>
                                <td>
                                    <select name="consumable_part_status" id="" class="form-control">
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
                        <th>تعداد خریداری شده</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consumableParts->whereIn('consumable_part_status', ['در انتظار خرید خارجی']) as $consumablePart)
                        <form action="{{ route('simpleWorkflowReport.consumable-parts.deliver', $consumablePart->id) }}"
                            method="post">
                            @csrf
                            @method('PUT')
                            <tr>
                                <td dir="ltr">{{ toJalali($consumablePart->created_at) }}</td>
                                <td>{{ $consumablePart->case_number }}</td>
                                <td>{{ $consumablePart->case()->customerName() }}</td>
                                <td>{{ $consumablePart->product_name }}</td>
                                <td>
                                    <input type="number" name="required_quantity_to_purchase" id=""
                                        value="{{ $consumablePart->required_quantity_to_purchase }}"
                                        class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="purchased_quantity" id=""
                                        value="{{ $consumablePart->purchased_quantity }}" class="form-control">
                                </td>
                                <td>
                                    <select name="consumable_part_status" id="" class="form-control">
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

@endsection

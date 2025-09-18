@extends('behin-layouts.app')

@section('title', 'گزارش چک ها')

@php
    $disableBackBtn = true;
@endphp

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <a href="javascript:history.back()" class="btn btn-outline-primary float-left">
                <i class="fa fa-arrow-left"></i> {{ trans('fields.Back') }}
            </a>
        </div>
    </div>
    <div class="card table-responsive">
        <div class="card-header bg-secondary text-center">
            <h3 class="card-title">گزارش چک ها</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="cheque-list">
                <thead>
                    <tr>
                        <th>شماره پرونده</th>
                        <th>نام مشتری (پرداخت کننده چک)</th>
                        <th>مبلغ چک</th>
                        <th>تاریخ سررسید</th>
                        <th>در وجه</th>
                        <th>شماره چک</th>
                        <th>گیرنده چک</th>
                        <th>توضیحات</th>
                        <th>پاس شد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chequeFromOnCredit as $group)
                        @php $first = $group->first(); @endphp
                        <tr @if ($group->every(fn($c) => $c->is_passed)) style="background-color: #d4edda;" class="passed" @endif>
                            <td>
                                @foreach ($group as $cheque)
                                    @if ($cheque->case_number)
                                        <a
                                            href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $cheque->case_number]) }}">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                        {{ $cheque->case_number }}
                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach ($group as $cheque)
                                    @if ($cheque->case_number)
                                        {{ $cheque->case()->getVariable('customer_workshop_or_ceo_name') }}
                                    @else
                                        {{ $cheque->description }}
                                    @endif
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ number_format($first->amount) }}</td>
                            <td>{{ toJalali((int) $first->cheque_due_date)->format('Y-m-d') }}</td>
                            <td>{{ $first->account_name }}</td>
                            <td>{{ $first->cheque_number }}</td>
                            <td>
                                @if ($first->cheque_receiver)
                                    {{ $first->cheque_receiver }}
                                @else
                                    <form method="POST"
                                        action="{{ route('simpleWorkflowReport.cheque-report.updateFromOnCredit', $first->id) }}"
                                        onsubmit="return confirm('آیا از ذخیره اطلاعات مطمئن هستید؟')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="fromOnCredit" id="" value="1">
                                        <input type="hidden" name="cheque_number" id=""
                                            value="{{ $first->cheque_number }}">
                                        <input type="text" name="cheque_receiver" class="form-control form-control-sm"
                                            required>
                                        <button type="submit" class="btn btn-sm btn-primary mt-1">ذخیره</button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $first->cheque_description ?? '' }}</td>
                            <td>
                                @if ($cheque->is_passed)
                                @else
                                    <form method="POST"
                                        action="{{ route('simpleWorkflowReport.cheque-report.updateFromOnCredit', $first->id) }}"
                                        onsubmit="return confirm('آیا از پاس شدن این چک مطمئن هستید؟')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="fromOnCredit" id="" value="1">
                                        <input type="hidden" name="is_passed" value="1">
                                        <button type="submit" class="btn btn-sm btn-success">پاس شد</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($cheques as $group)
                        @php $first = $group->first(); @endphp
                        <tr @if ($group->every(fn($c) => $c->is_passed)) style="background-color: #d4edda;" class="passed" @endif>
                            <td>
                                @foreach ($group as $cheque)
                                    @if ($cheque->case_number)
                                        <a
                                            href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $cheque->case_number]) }}">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                        {{ $cheque->case_number }}
                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach ($group as $cheque)
                                    @if ($cheque->case_number)
                                        {{ $cheque->case()->getVariable('customer_workshop_or_ceo_name') }}
                                    @else
                                        {{ $cheque->description }}
                                    @endif
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach ($group as $cheque)
                                    {{ number_format($cheque->cost) }}
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach ($group as $cheque)
                                    {{ toJalali((int) $cheque->cheque_due_date)->format('Y-m-d') }}
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>

                            <td>
                                @foreach ($group as $cheque)
                                    {{ $cheque->destination_account_name }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                            {{-- شماره چک --}}
                            <td>
                                @if ($first->cheque_number)
                                    {{ $first->cheque_number }}
                                @else
                                    @foreach ($group as $cheque)
                                        <form method="POST"
                                            action="{{ route('simpleWorkflowReport.cheque-report.update', $cheque->id) }}"
                                            onsubmit="return confirm('آیا از ذخیره اطلاعات مطمئن هستید؟')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="cheque_number" class="form-control form-control-sm"
                                                required>
                                            <button type="submit" class="btn btn-sm btn-primary mt-1">ذخیره</button>
                                        </form>
                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>

                            {{-- گیرنده چک --}}
                            <td>
                                @foreach ($group as $cheque)
                                    @if ($cheque->cheque_receiver)
                                        {{ $cheque->cheque_receiver }}
                                    @else
                                        <form method="POST"
                                            action="{{ route('simpleWorkflowReport.cheque-report.update', $cheque->id) }}"
                                            onsubmit="return confirm('آیا از ذخیره اطلاعات مطمئن هستید؟')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="cheque_receiver"
                                                class="form-control form-control-sm" required>
                                            <button type="submit" class="btn btn-sm btn-primary mt-1">ذخیره</button>
                                        </form>
                                    @endif
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>

                            <td>
                                @foreach ($group as $cheque)
                                    {{ $cheque->description }}
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>

                            {{-- دکمه پاس شد --}}
                            <td>
                                @foreach ($group as $cheque)
                                    @if ($cheque->is_passed)
                                        {{-- <span class="badge bg-success">پاس شد</span> --}}
                                    @else
                                        <form method="POST"
                                            action="{{ route('simpleWorkflowReport.cheque-report.update', $cheque->id) }}"
                                            onsubmit="return confirm('آیا از پاس شدن این چک مطمئن هستید؟')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_passed" value="1">
                                            <button type="submit" class="btn btn-sm btn-success">پاس شد</button>
                                        </form>
                                    @endif
                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right">آمار این صفحه:</th>
                        <th id="page-total"></th>
                        <th colspan="6"></th>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#cheque-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            "order": [
                [3, "desc"]
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                var intVal = function(i) {
                    if (typeof i === 'string') {
                        return parseInt(i.replace(/,/g, '')) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };

                var settledTotal = 0;
                var unsettledTotal = 0;
                var settledCount = 0;
                var unsettledCount = 0;

                api.rows({
                    page: 'current'
                }).every(function() {
                    var rowNode = $(this.node());
                    var amount = this.data()[2]; // ستون مبلغ
                    var cost = intVal(amount);

                    if (rowNode.hasClass('passed')) {
                        settledTotal += cost;
                        settledCount++;
                    } else {
                        unsettledTotal += cost;
                        unsettledCount++;
                    }
                });

                // نمایش در فوتر
                $(api.column(2).footer()).html(
                    `<div>
                <span class="text-success">پاس‌شده: ${settledTotal.toLocaleString('fa-IR')} ریال (تعداد: ${settledCount})</span><br>
                <span class="text-danger">پاس‌نشده: ${unsettledTotal.toLocaleString('fa-IR')} ریال (تعداد: ${unsettledCount})</span>
            </div>`
                );
            }
        });
    </script>
@endsection

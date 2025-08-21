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
                        <th>نام مشتری</th>
                        <th>مبلغ</th>
                        <th>تاریخ</th>
                        <th>مقصد حساب</th>
                        <th>شماره چک</th>
                        <th>گیرنده چک</th>
                        <th>توضیحات</th>
                        <th>پاس شد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cheques as $group)
                        @php $first = $group->first(); @endphp
                        <tr @if ($group->every(fn($c) => $c->is_passed)) style="background-color: #d4edda;" @endif>
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
                                    {{ $cheque->case()->getVariable('customer_workshop_or_ceo_name') ?? $cheque->description }}
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
            }
        });
    </script>
@endsection

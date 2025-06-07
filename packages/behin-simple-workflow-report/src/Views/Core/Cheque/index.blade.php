@extends('behin-layouts.app')

@section('title', 'گزارش چک ها')

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
    <div class="table-responsive">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>شماره پرونده</th>
                    <th>نام مشتری</th>
                    <th>مبلغ</th>
                    <th>تاریخ</th>
                    <th>شماره چک</th>
                    <th>گیرنده چک</th>
                    <th>توضیحات</th>
                    <th>پاس شد</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cheques as $cheque)
                    <tr>
                        <td>
                            <a
                                href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $cheque->case_number]) }}">
                                <i class="fa fa-external-link"></i>
                            </a>
                            {{ $cheque->case_number }}
                        </td>
                        <td>{{ $cheque->case()->getVariable('customer_workshop_or_ceo_name') }}</td>
                        <td>{{ number_format($cheque->cost) }}</td>
                        <td>{{ toJalali((int) $cheque->cheque_due_date)->format('Y-m-d') }}</td>

                        {{-- شماره چک --}}
                        <td>
                            @if ($cheque->cheque_number)
                                {{ $cheque->cheque_number }}
                            @else
                                <form method="POST"
                                    action="{{ route('simpleWorkflowReport.cheque-report.update', $cheque->id) }}"
                                    onsubmit="return confirm('آیا از ذخیره اطلاعات مطمئن هستید؟')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="cheque_number" class="form-control form-control-sm" required>
                                    <button type="submit" class="btn btn-sm btn-primary mt-1">ذخیره</button>
                                </form>
                            @endif
                        </td>

                        {{-- گیرنده چک --}}
                        <td>
                            @if ($cheque->cheque_receiver)
                                {{ $cheque->cheque_receiver }}
                            @else
                                <form method="POST"
                                    action="{{ route('simpleWorkflowReport.cheque-report.update', $cheque->id) }}"
                                    onsubmit="return confirm('آیا از ذخیره اطلاعات مطمئن هستید؟')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="cheque_receiver" class="form-control form-control-sm"
                                        required>
                                    <button type="submit" class="btn btn-sm btn-primary mt-1">ذخیره</button>
                                </form>
                            @endif
                        </td>

                        <td>{{ $cheque->description }}</td>

                        {{-- دکمه پاس شد --}}
                        <td>
                            @if ($cheque->is_passed)
                                <span class="badge bg-success">پاس شد</span>
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

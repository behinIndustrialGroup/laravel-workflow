@extends('behin-layouts.app')

@section('title', 'گزارش حساب دفتری')


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
            <h3 class="card-title">گزارش حساب دفتری</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="on-credit-list">
                <thead>
                    <tr>
                        <th>شماره پرونده</th>
                        <th>نام مشتری</th>
                        <th>مبلغ</th>
                        <th>تاریخ</th>
                        <th>توضیحات</th>
                        <th>تسویه شد</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalCost = 0;
                    @endphp
                    @foreach ($onCredits as $onCredit)
                        <tr @if($onCredit->is_passed) style="background-color: #d4edda;" @endif>
                            <td>
                                <a
                                    href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $onCredit->case_number]) }}">
                                    <i class="fa fa-external-link"></i>
                                </a>
                                {{ $onCredit->case_number }}
                            </td>
                            <td>{{ $onCredit->case()->getVariable('customer_workshop_or_ceo_name') }}</td>
                            <td>
                                @php
                                    $totalCost += (int) str_replace(',', '',$onCredit->cost);
                                @endphp
                                {{ number_format($onCredit->cost) }}
                            </td>
                            <td>{{ toJalali((int) $onCredit->fix_cost_date)->format('Y-m-d') }}</td>



                            <td>{{ $onCredit->description }}</td>

                            {{-- دکمه پاس شد --}}
                            <td>
                                @if ($onCredit->is_passed)
                                    {{-- <span class="badge bg-success">تسویه شد</span> --}}
                                @else
                                    <form method="POST"
                                        action="{{ route('simpleWorkflowReport.on-credit-report.update', $onCredit->id) }}"
                                        onsubmit="return confirm('آیا از تسویه شدن این حساب دفتری مطمئن هستید؟')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_passed" value="1">
                                        <button type="submit" class="btn btn-sm btn-success">تسویه شد</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-success">
                        <td></td>
                        <td>مجموع</td>
                        <td>{{ number_format($totalCost) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#on-credit-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection

@extends('behin-layouts.app')


@section('title')
    گزارش مالی
@endsection

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;
    use Morilog\Jalali\Jalalian;
    use Behin\SimpleWorkflowReport\Helper\ReportHelper;

    $today = Carbon::today();
    $todayShamsi = Jalalian::fromCarbon($today);
    $thisYear = $todayShamsi->getYear();
    $thisMonth = $todayShamsi->getMonth();
    $thisMonth = str_pad($thisMonth, 2, '0', STR_PAD_LEFT);
    $to = Jalalian::fromFormat('Y-m-d', "$thisYear-$thisMonth-01")
        ->addMonths(1)
        ->subDays(1)
        ->format('Y-m-d');

    $from = isset($_GET['from']) ? $_GET['from'] : "$thisYear-$thisMonth-01";
    $to = isset($_GET['to']) ? $_GET['to'] : (string) $to;
    $quser = isset($_GET['quser']) ? $_GET['quser'] : null;

    // دریافت جدول اصلی
    $finTable = ReportHelper::getFilteredFinTable($from, $to, $quser);
    // dd($finTable);
    // پردازش آمار کاربران
    $users = DB::table('users')
        ->get()
        ->each(function ($user) use ($finTable) {
            $userItems = $finTable->where('mapa_expert_id', $user->id);
            $user->total_external_repair_cost = $userItems->sum('repair_cost');
            $user->total_internal_fix_cost = $userItems->sum('fix_cost');
            $user->total_income = $user->total_external_repair_cost + $user->total_internal_fix_cost;
            $user->repairs_done = $userItems->whereNotNull('fix_report_date')->count();
            $user->repairs_pending = $userItems->whereNull('fix_report_date')->count();
        });

@endphp


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <a href="javascript:history.back()" class="btn btn-outline-primary float-left">
                            <i class="fa fa-arrow-left"></i> {{ trans('fields.Back') }}
                        </a>
                    </div>
                </div>

                {{-- کل دریافتی ها --}}
                <button class="btn btn-primary"
                    onclick="window.location.href='{{ route('simpleWorkflowReport.fin.allPayments') }}'">

                </button>
                {{-- @include('SimpleWorkflowReportView::Core.Summary.process.partial.all-payments') --}}


                {{-- عملکرد مالی پرسنل --}}
                <div class="">
                    <div class="card">
                        <div class="card-header bg-success text-center">
                            عملکرد مالی پرسنل
                        </div>
                        <div class="card-header bg-light">
                            <form action="{{ url()->current() }}" class="form-row align-items-end">
                                <div class="form-group col-md-2">
                                    <label for="year">از</label>
                                    <input type="text" name="from" value="{{ $from }}"
                                        class="form-control persian-date">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="year">تا</label>
                                    <input type="text" name="to" value="{{ $to }}"
                                        class="form-control persian-date">
                                </div>


                                <div class="form-group col-md-2">
                                    <label for="quser">کاربر</label>
                                    <select name="quser" id="quser" class="select2 form-control">
                                        <option value="">{{ trans('fields.All') }}
                                        </option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ $user->id == $quser ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <button type="submit" class="btn btn-primary btn-block">فیلتر</button>
                                </div>
                            </form>
                        </div>

                        {{-- <div class="card-body table-responsive">
                            <table class="table" id="mapa-expert">
                                <thead>
                                    <tr>
                                        <td>{{ trans('fields.user_number') }}</td>
                                        <td>{{ trans('fields.user_name') }}</td>
                                        <td>{{ trans('fields.total_income') }}</td>
                                        <td>{{ trans('fields.repairs_done') }}</td>
                                        <td>{{ trans('fields.repairs_pending') }}</td>
                                        <td>{{ trans('fields.Action') }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalIncome = 0;
                                    @endphp
                                    @foreach ($users as $user)
                                        @if ($quser)
                                            @if ($quser == $user->id)
                                                <tr>
                                                    <td>{{ $user->number }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ number_format($user->total_income) }}</td>
                                                    <td>{{ $user->repairs_done }}</td>
                                                    <td>{{ $user->repairs_pending }}</td>
                                                    <td></td>
                                                </tr>
                                                @php
                                                    $totalIncome += $user->total_income;
                                                @endphp
                                            @endif
                                        @else
                                            <tr>
                                                <td>{{ $user->number }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ number_format($user->total_income) }}</td>
                                                <td>{{ $user->repairs_done }}</td>
                                                <td>{{ $user->repairs_pending }}</td>
                                                <td>
                                                    <a href="{{ url()->current() . "?month=$month&year=$year&quser=$user->id" }}" class="btn btn-sm btn-info">{{ trans('fields.Show More') }}</a>
                                                </td>
                                            </tr>
                                            @php
                                                $totalIncome += $user->total_income;
                                            @endphp
                                        @endif
                                    @endforeach
                                    <tr class="bg-success">
                                        <td>1000</td>
                                        <td>مجموع</td>
                                        <td>{{ number_format($totalIncome) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> --}}
                    </div>

                </div>

                {{-- گزارش کل مجموع هزینه های دریافت شده --}}
                <div class="">
                    <div class="card">
                        <div class="card-header bg-success text-center">
                            گزارش مجموع هزینه های دریافت شده
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table" id="total-cost">
                                <thead>
                                    <tr>
                                        <th>{{ trans('fields.case_number') }}</th>
                                        <th>{{ trans('fields.process') }}</th>
                                        <th>{{ trans('fields.mapa_expert') }}</th>
                                        <th>{{ trans('fields.repair_date') }}</th>
                                        <th>{{ trans('fields.Declared Cost') }}</th>
                                        <th>{{ trans('fields.payment_amount') }}</th>
                                        <th>{{ trans('fields.payment_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRepairCost = 0;
                                        $totalPaymentAmount = 0;
                                        $numberOfInternalProcess = 0;
                                        $numberOfExternalProcess = 0;
                                    @endphp
                                    @foreach ($finTable as $row)
                                        {{-- فرایند تعمیر در محل --}}
                                        @if ($row->process_id == '35a5c023-5e85-409e-8ba4-a8c00291561c')
                                            <tr>

                                                <td>{{ $row->number }}
                                                    <a href="{{ route('simpleWorkflowReport.summary-report.edit', $row->case_id) }}"
                                                        target="_blank">
                                                        <i class="fa fa-external-link"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $row->customer }}</td>
                                                <td>{{ $row->process_name }}</td>
                                                <td>{{ $row->mapa_expert_name }}</td>
                                                <td>{{ $row->fix_report_date ? toJalali((int)$row->fix_report_date)->format('Y-m-d') : trans('fields.not_available') }}
                                                </td>
                                                <td {{ is_numeric($row->fix_cost) ? 'bg-danger' : '' }}>
                                                    {{ number_format($row->fix_cost) }}
                                                    @if ($row->fix_cost_2)
                                                        <br>
                                                        {{ number_format($row->fix_cost_2) }}
                                                    @endif
                                                    @if ($row->fix_cost_3)
                                                        <br>
                                                        {{ number_format($row->fix_cost_3) }}
                                                    @endif
                                                </td>
                                                <td>{{ $row->payment_amount }}</td>
                                                <td>{{ $row->payment_date ?? '' }}</td>
                                                @php
                                                    $totalRepairCost += $row->fix_cost;
                                                    $totalPaymentAmount += $row->payment_amount;
                                                    $numberOfExternalProcess++;
                                                @endphp
                                            </tr>
                                        @endif
                                        {{-- فرایند تعمیر در مدارپرداز --}}
                                        @if ($row->process_id == '4bb6287b-9ddc-4737-9573-72071654b9de')
                                            <tr>
                                                <td>{{ $row->number }}
                                                    <a href="{{ route('simpleWorkflowReport.summary-report.edit', $row->case_id) }}"
                                                        target="_blank">
                                                        <i class="fa fa-external-link"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $row->customer }}</td>
                                                <td>{{ $row->process_name }}</td>
                                                <td>{{ $row->mapa_expert_name }}</td>
                                                <td>{{ $row->fix_report_date ? toJalali((int)$row->fix_report_date)->format('Y-m-d') : trans('fields.not_available') }}
                                                </td>
                                                <td {{ is_numeric($row->fix_cost) ? 'bg-danger' : '' }}>
                                                    {{ number_format($row->fix_cost) }}
                                                    @if ($row->fix_cost_2)
                                                        <br>
                                                        {{ number_format($row->fix_cost_2) }}
                                                    @endif
                                                    @if ($row->fix_cost_3)
                                                        <br>
                                                        {{ number_format($row->fix_cost_3) }}
                                                    @endif
                                                </td>
                                                <td>{{ $row->payment_amount }}</td>
                                                <td>{{ $row->payment_date ?? '' }}</td>
                                                @php
                                                    $totalRepairCost += $row->fix_cost;
                                                    $totalPaymentAmount += $row->payment_amount;
                                                    $numberOfInternalProcess++;
                                                @endphp
                                        @endif
                                        </tr>
                                    @endforeach
                                <tfoot>
                                    <tr class="bg-success">
                                        <td></td>
                                        <td>
                                            داخلی: {{ $numberOfInternalProcess }}<br>
                                            خارجی: {{ $numberOfExternalProcess }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td>مجموع</td>
                                        <td>{{ number_format($totalRepairCost) }}</td>
                                        <td>{{ number_format($totalPaymentAmount) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
        $(document).ready(function() {
            $('#total-cost').DataTable({
                "dom": 'Bfrtip',
                "buttons": [{
                    "extend": 'excelHtml5',
                    "text": "خروجی اکسل",
                    "title": "گزارش مجموع هزینه های دریافت شده به ازای کارشناس",
                    "className": "btn btn-success btn-sm",
                    "exportOptions": {
                        "columns": ':visible',
                        "footer": true
                    }
                }, ],

                "pageLength": -1,
                "order": [
                    [0, "desc"]
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                },
            });
            $('#mapa-expert').DataTable({
                "dom": 'Bfrtip',
                "buttons": [{
                    "extend": 'excelHtml5',
                    "text": "خروجی اکسل",
                    "title": "گزارش مجموع هزینه های دریافت شده به ازای کارشناس",
                    "className": "btn btn-success btn-sm",
                    "exportOptions": {
                        "columns": ':visible',
                        "footer": true
                    }
                }, ],
                "searching": false,
                "pageLength": -1,
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                },
            });
        });
    </script>
@endsection

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

    $year = isset($_GET['year']) ? $_GET['year'] : $thisYear;
    $month = isset($_GET['month']) ? $_GET['month'] : $thisMonth;
    $quser = isset($_GET['quser']) ? $_GET['quser'] : null;

    // دریافت جدول اصلی
    $finTable = ReportHelper::getFilteredFinTable($year, $month, $quser);

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
                {{-- عملکرد مالی پرسنل --}}
                {{-- <div class="">
                    <div class="card">
                        <div class="card-header bg-success text-center">
                            عملکرد مالی پرسنل
                        </div>
                        <div class="card-header bg-light">
                            <form action="{{ url()->current() }}" class="form-inline">
                                <div class="form-group col-sm-3 ">
                                    <label for="year" class="mr-2">سال</label>
                                    <select name="year" id="year" class="form-control">
                                        @for ($i = $thisYear; $i >= 1403; $i--)
                                            <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group col-sm-3">
                                    <label for="month" class="mr-2">ماه</label>
                                    <select name="month" id="month" class="form-control">
                                        <option value="">{{ trans('fields.All') }}</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group col-sm-3">
                                    <label for="quser" class="mr-2">کاربر</label>
                                    <div class="col-sm-9">
                                        <select name="quser" id="quser" class="select2">
                                            <option value="">{{ trans('fields.All') }}
                                            </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ $user->id == $quser ? 'selected' : '' }}>{{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <input type="submit" class="btn btn-primary col-sm-3" value="جستجو">
                            </form>
                        </div>
                        <div class="card-body table-responsive">
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
                        </div>
                    </div>

                </div> --}}

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
                                        <th>{{ trans('fields.repair_cost') }}</th>
                                        <th>{{ trans('fields.payment_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRepairCost = 0;
                                        $totalPaymentAmount = 0;
                                    @endphp
                                    @foreach ($finTable as $row)
                                        <tr>
                                            {{-- فرایند تعمیر در محل --}}
                                            @if ($row->process_id == '35a5c023-5e85-409e-8ba4-a8c00291561c')
                                                <td>{{ $row->number }}
                                                    <a href="{{ route('simpleWorkflowReport.summary-report.edit', $row->case_id) }}"
                                                        target="_blank">
                                                        <i class="fa fa-external-link"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $row->process_name }}</td>
                                                <td>{{ $row->mapa_expert_name }}</td>
                                                <td>{{ $row->fix_report_date ? toJalali($row->fix_report_date)->format('Y-m-d') : trans('fields.not_available') }}
                                                </td>
                                                <td>{{ number_format($row->fix_cost) }}</td>
                                                <td>{{ $row->payment_amount }}</td>
                                                @php
                                                    $totalRepairCost += $row->repair_cost;
                                                @endphp
                                            @endif
                                            {{-- فرایند تعمیر در مدارپرداز --}}
                                            @if ($row->process_id == '4bb6287b-9ddc-4737-9573-72071654b9de')
                                                <td>{{ $row->number }}
                                                    <a href="{{ route('simpleWorkflowReport.summary-report.edit', $row->case_id) }}"
                                                        target="_blank">
                                                        <i class="fa fa-external-link"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $row->process_name }}</td>
                                                <td>{{ $row->mapa_expert_name }}</td>
                                                <td>{{ $row->fix_report_date ? toJalali($row->fix_report_date)->format('Y-m-d') : trans('fields.not_available') }}
                                                </td>
                                                <td>{{ number_format($row->fix_cost) }}</td>
                                                <td>{{ $row->payment_amount }}</td>
                                                @php
                                                    $totalRepairCost += $row->fix_cost;
                                                @endphp
                                            @endif
                                        </tr>
                                    @endforeach
                                    <tr class="bg-success">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>مجموع</td>
                                        <td>{{ number_format($totalRepairCost) }}</td>
                                        <td>{{ number_format($totalPaymentAmount) }}</td>
                                    </tr>
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

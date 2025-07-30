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
                                        <th>{{ trans('fields.customer') }}</th>
                                        <th>{{ trans('fields.process') }}</th>
                                        <th>{{ trans('fields.mapa_expert') }}</th>
                                        <th>{{ trans('fields.fix_cost_date') }}</th>
                                        <th>{{ trans('fields.Declared Cost') }}</th>
                                        <th>تفکیک هزینه ها</th>
                                        <th>{{ trans('fields.payment_amount') }}</th>
                                        <th>{{ trans('fields.payment_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRepairCost = 0;
                                        $totalPaymentAmount = 0;
                                        $totalCaseCosts = 0;
                                        $numberOfInternalProcess = 0;
                                        $numberOfExternalProcess = 0;
                                    @endphp
                                    @foreach ($finTable as $row)
                                        <tr>

                                            <td>{{ $row->case_number }}
                                                <a
                                                    href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $row->case_number]) }}"><i
                                                        class="fa fa-external-link"></i></a>
                                            </td>
                                            <td>{{ $row->customer ?? '' }}</td>
                                            <td>{{ $row->process_name ?? '' }}</td>
                                            <td>
                                                @foreach ($row->in_mapa_experts as $in_mapa_expert)
                                                {{ getUserInfo($in_mapa_expert)?->name ? getUserInfo($in_mapa_expert)->name : $in_mapa_expert }}<br>
                                                @endforeach
                                                @foreach ($row->out_mapa_experts as $out_mapa_expert)
                                                    {{ getUserInfo($out_mapa_expert)?->name ? getUserInfo($out_mapa_expert)->name : $out_mapa_expert }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ $row->fix_cost_date ? toJalali((int) $row->fix_cost_date)->format('Y-m-d') : trans('fields.not_available') }}
                                            </td>
                                            <td {{ is_numeric($row->total_cost) ? 'bg-danger' : '' }}>
                                                {{ number_format($row->total_cost) }}
                                            </td>
                                            <td>
                                                @foreach ($row->case_costs as $case_cost)
                                                    @php
                                                        $totalCaseCosts += (int)(str_replace(',', '', $case_cost->amount));
                                                    @endphp
                                                    {{ $case_cost->counterparty()?->name }} ({{ $case_cost->amount }})
                                                @endforeach
                                            </td>
                                            <td>{{ $row->total_payment ? number_format($row->total_payment) : '' }}</td>
                                            <td>{{ $row->payment_date ? toJalali((int) $row->payment_date)->format('Y-m-d') : trans('fields.not_available') }}</td>
                                            @php
                                                $totalRepairCost += $row->total_cost;
                                                $totalPaymentAmount += $row->total_payment;
                                                if($row->process_id == "4bb6287b-9ddc-4737-9573-72071654b9de" or $row->process_name == "داخلی"){
                                                    $numberOfInternalProcess++;
                                                }
                                                if($row->process_id == "35a5c023-5e85-409e-8ba4-a8c00291561c" or $row->process_name == "خارجی"){
                                                    $numberOfExternalProcess++;
                                                }
                                            @endphp
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
                                        <td>{{ number_format($totalCaseCosts) }}</td>
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

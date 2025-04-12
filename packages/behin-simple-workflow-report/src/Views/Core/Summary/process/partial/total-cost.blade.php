@extends('behin-layouts.app')


@section('title')
    گزارش مالی
@endsection

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;
    use Morilog\Jalali\Jalalian;

    $today = Carbon::today();
    $todayShamsi = Jalalian::fromCarbon($today);
    $thisYear = $todayShamsi->getYear();
    $thisMonth = $todayShamsi->getMonth();

    function getFilteredFinTable($year = null, $month = null)
    {
        $mapaSubquery = DB::table('wf_variables')
            ->select('case_id', DB::raw('MAX(value) as mapa_expert_id'))
            ->where('key', 'mapa_expert')
            ->groupBy('case_id');

        $query = DB::table('wf_variables')
            ->join('wf_cases', 'wf_variables.case_id', '=', 'wf_cases.id')
            ->leftJoinSub($mapaSubquery, 'mapa', function ($join) {
                $join->on('wf_variables.case_id', '=', 'mapa.case_id');
            })
            ->leftJoin('users', 'mapa.mapa_expert_id', '=', 'users.id')
            ->leftJoin('wf_process', 'wf_cases.process_id', '=', 'wf_process.id')
            ->select(
                'wf_variables.case_id',
                'wf_cases.number',
                'wf_process.name as process_name',
                DB::raw("MAX(CASE WHEN `key` = 'customer_workshop_or_ceo_name' THEN `value` ELSE '' END) AS customer"),
                DB::raw("MAX(CASE WHEN `key` = 'repair_cost' THEN `value` ELSE 0 END) AS repair_cost"),
                DB::raw("MAX(CASE WHEN `key` = 'payment_amount' THEN `value` ELSE 0 END) AS payment_amount"),
                DB::raw("MAX(CASE WHEN `key` = 'visit_date' THEN `value` ELSE 0 END) AS visit_date"),
                DB::raw("MAX(CASE WHEN `key` = 'visit_date' THEN `value` ELSE 0 END) AS visit_date"),
                'users.name as mapa_expert_name',
                'users.id as mapa_expert_id',
            )
            ->groupBy('wf_variables.case_id', 'wf_cases.number', 'users.name')
            ->havingRaw('repair_cost != 0');

        if ($year) {
            $query->havingRaw('visit_date LIKE ?', ["%{$year}%"]);
        }

        if ($month) {
            $query->havingRaw('visit_date LIKE ?', ["%-{$month}-%"]);
        }

        return $query->get();
    }

    $year = request()->get('year');
    $month = request()->get('month');
    $finTable = getFilteredFinTable($year, $month);
    // استخراج هزینه کلی برای هر کاربر
    $users = DB::table('users')
        ->get()
        ->each(function ($user) use ($finTable) {
            $user->total_repair_cost = $finTable->where('mapa_expert_id', $user->id)->sum('repair_cost');
            $user->total_repair = $finTable->where('mapa_expert_id', $user->id)->count();
            $user->total_repair_pendding = $finTable->where('mapa_expert_id', $user->id)->whereNull('repair_cost')->count();
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
                <div class="table-responsive">
                    <div class="card">
                        <div class="card-header bg-success text-center">
                            عملکرد مالی پرسنل
                        </div>
                        <div class="card-header bg-light">
                            <form action="{{ url()->current() }}" class=" row col-sm-12">
                                <div class="col-sm-3">
                                    سال
                                    <input type="text" name="year" id="" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    ماه
                                    <input type="text" name="month" id="" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <input type="submit" class="btn btn-sm btn-primary" value="جستجو">
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table class="table" id="mapa-expert">
                                <thead>
                                    <tr>
                                        <td>{{ trans('fields.user_number') }}</td>
                                        <td>{{ trans('fields.user_name') }}</td>
                                        <td>{{ trans('fields.year') }}</td>
                                        <td>{{ trans('fields.month') }}</td>
                                        <td>{{ trans('fields.total_repair_cost') }}</td>
                                        <td>{{ trans('fields.total_repair') }}</td>
                                        <td>{{ trans('fields.total_repair_pendding') }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->number }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $thisYear }}</td>
                                            <td>{{ $thisMonth }}</td>
                                            <td>{{ number_format($user->total_repair_cost) }}</td>
                                            <td>{{ $user->total_repair }}</td>
                                            <td>{{ $user->total_repair_pendding }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- گزارش کل مجموع هزینه های دریافت شده --}}
                <div class="table-responsive">
                    <div class="card">
                        <div class="card-header bg-success text-center">
                            گزارش مجموع هزینه های دریافت شده
                        </div>
                        <div class="card-body">
                            <table class="table" id="total-cost">
                                <thead>
                                    <tr>
                                        <th>{{ trans('fields.case_number') }}</th>
                                        <th>{{ trans('fields.process') }}</th>
                                        <th>{{ trans('fields.customer') }}</th>
                                        <th>{{ trans('fields.mapa_expert') }}</th>
                                        <th>{{ trans('fields.visit_date') }}</th>
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
                                            <td>{{ $row->number }}</td>
                                            <td>{{ $row->process_name }}</td>
                                            <td>{{ $row->customer }}</td>
                                            <td>{{ $row->mapa_expert_name }}</td>
                                            <td>{{ convertPersianToEnglish($row->visit_date) }}</td>
                                            <td>{{ number_format($row->repair_cost) }}</td>
                                            <td>{{ $row->payment_amount }}</td>
                                        </tr>
                                        @php
                                            $totalRepairCost += $row->repair_cost;
                                        @endphp
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
                    [1, "desc"]
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                },
            });
        });
    </script>
@endsection

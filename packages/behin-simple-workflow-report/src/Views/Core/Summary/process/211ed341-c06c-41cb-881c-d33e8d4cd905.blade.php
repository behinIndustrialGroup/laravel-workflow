@extends('behin-layouts.app')

@section('title')
    خلاصه گزارش فرایند {{ $process->name }}
@endsection

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;
    use Morilog\Jalali\Jalalian;
    use App\Models\User;
    use Behin\SimpleWorkflow\Models\Entities\Timeoffs;

    $today = Carbon::today();
    $todayShamsi = Jalalian::fromCarbon($today);
    $thisYear = $todayShamsi->getYear();
    $thisMonth = $todayShamsi->getMonth();
    $totalLeaves = $thisMonth * 20;
    $users = DB::table('users')->get();
    $users = User::get();
    foreach ($users as $user) {
        $approvedLeaves = Timeoffs::select(
            DB::raw(
                'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.type = "ساعتی" THEN duration ELSE duration*8 END), 0) as total_leaves',
            ),
        )
            ->where('user', $user->id)
            ->where(function ($query) use ($thisYear) {
                $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
            })
            ->where('approved', 1)
            ->first()->total_leaves;

        $user->approvedLeaves = $approvedLeaves;
        $restLeaves = $thisMonth * 20 - $approvedLeaves;
        $user->restLeaves = $restLeaves;
    }
    foreach ($users as $user) {
        $approvedLeaves = DB::table('wf_entity_timeoffs')
            ->select(
                DB::raw(
                    'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.type = "ساعتی" THEN duration ELSE duration*8 END), 0) as total_leaves',
                ),
            )
            ->where('user', $user->id)
            ->where(function ($query) use ($thisYear) {
                $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
            })
            ->where('approved', 1)
            ->first()->total_leaves;
        $user->approvedLeaves = $approvedLeaves;
        $restLeaves = $thisMonth * 20 - $approvedLeaves;
        $user->restLeaves = $restLeaves;

        $user->hourlyTimeoffs = Timeoffs::where(function ($query) use ($thisYear) {
                    $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
                })
                ->where('approved',1)
                ->get();
    }

    // $monthlyLeaves = DB::table('users')
    //     ->leftJoin('wf_entity_timeoffs', function ($join) use ($thisYear) {
    //         $join
    //             ->on('users.id', '=', 'wf_entity_timeoffs.user')
    //             ->where(function ($query) use ($thisYear) {
    //                 $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
    //             })
    //             ->where('approved', 1);
    //     })
    //     ->select(
    //         'users.id as user_id',
    //         'users.name as user_name',
    //         'wf_entity_timeoffs.start_year',
    //         'wf_entity_timeoffs.start_month',
    //         DB::raw(
    //             'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.approved = 1 THEN duration ELSE 0 END), 0) as approved_leaves',
    //         ),
    //         DB::raw(
    //             'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.approved = 0 THEN duration ELSE 0 END), 0) as pending_or_rejected_leaves',
    //         ),
    //         DB::raw(
    //             'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.type = "ساعتی" THEN duration ELSE duration*8 END), 0) as total_leaves',
    //         ),
    //     )
    //     ->groupBy('users.id', 'users.name', 'wf_entity_timeoffs.start_year')
    //     ->orderBy('wf_entity_timeoffs.start_year', 'desc')
    //     ->orderBy('wf_entity_timeoffs.start_month', 'desc')
    //     ->get();

    $today = Carbon::today();
    $todayShamsi = Jalalian::fromCarbon($today);
    $thisYear = $todayShamsi->getYear();
    $thisMonth = $todayShamsi->getMonth();
    $totalLeaves = $thisMonth * 20;

@endphp


@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row justify-content-center">

            <div class="col-md-12">
                @if (auth()->user()->access('خلاصه گزارش فرایند: مرخصی > گزارش ماهانه مرخصی کاربران'))
                    <div class="card">
                        {{-- @php
                            $hourlyLeaves = [];
                            $thisMonthLeaves = [];
                        @endphp

                        @if (isset($_GET['userId']))
                            <a href="{{ route('simpleWorkflowReport.summary-report.show', $process->id) }}">
                                <button class="btn btn-primary btn-sm">{{ trans('fields.Back') }}</button>
                            </a>
                            @php
                                $isFiltered = true;
                                $user = getUserInfo($_GET['userId']);
                                $tableName = $user->name;
                            @endphp
                            @foreach ($process->cases as $case)
                                @if (
                                    $case->getVariable('timeoff_request_type') === 'ساعتی' &&
                                        $case->getVariable('department_manager') &&
                                        $case->getVariable('user_department_manager_approval') &&
                                        $case->creator == $_GET['userId']
                                )
                                    @php
                                        $start_date = convertPersianToEnglish(
                                            $case->getVariable('timeoff_hourly_request_start_date'),
                                        );
                                        $startMonth = Jalalian::fromFormat('Y-m-d', $start_date)->format('%m');
                                    @endphp
                                    @if ($thisMonth == $startMonth)
                                        @php
                                            $hourlyLeaves[] = $case;
                                        @endphp
                                    @endif
                                @endif
                                @if (
                                    $case->getVariable('timeoff_request_type') === 'روزانه' &&
                                        $case->getVariable('department_manager') &&
                                        $case->getVariable('user_department_manager_approval') &&
                                        $case->creator == $_GET['userId']
                                )
                                    @php
                                        $today = Carbon::today();
                                        $start_date = convertPersianToEnglish($case->getVariable('timeoff_start_date'));
                                        $startMonth = Jalalian::fromFormat('Y-m-d', $start_date)->format('%m');
                                        $end_date = convertPersianToEnglish($case->getVariable('timeoff_end_date'));
                                        $endMonth = Jalalian::fromFormat('Y-m-d', $end_date)->format('%m');
                                    @endphp
                                    @if ($thisMonth == $startMonth || $thisMonth == $endMonth)
                                        @php
                                            $thisMonthLeaves[] = $case;
                                        @endphp
                                    @endif
                                @endif
                            @endforeach
                        @else
                            @foreach ($process->cases as $case)
                                @if (
                                    $case->getVariable('timeoff_request_type') === 'ساعتی' &&
                                        $case->getVariable('department_manager') &&
                                        $case->getVariable('user_department_manager_approval'))
                                    @php
                                        $today = Carbon::today();
                                        $start_date = convertPersianToEnglish(
                                            $case->getVariable('timeoff_hourly_request_start_date'),
                                        );
                                        $gregorianStartDate = Jalalian::fromFormat('Y-m-d', $start_date)
                                            ->toCarbon()
                                            ->format('Y-m-d');
                                        $diff = $today->diffInDays($gregorianStartDate);
                                    @endphp
                                    @if ($diff >= 0)
                                        @php
                                            $hourlyLeaves[] = $case;
                                        @endphp
                                    @endif
                                @endif
                                @if (
                                    $case->getVariable('timeoff_request_type') === 'روزانه' &&
                                        $case->getVariable('department_manager') &&
                                        $case->getVariable('user_department_manager_approval'))
                                    @php
                                        $today = Carbon::today();
                                        $start_date = convertPersianToEnglish($case->getVariable('timeoff_end_date'));
                                        $gregorianStartDate = Jalalian::fromFormat('Y-m-d', $start_date)
                                            ->toCarbon()
                                            ->format('Y-m-d');
                                        $diff = $today->diffInDays($gregorianStartDate);
                                    @endphp
                                    @if ($diff >= 0)
                                        @php
                                            $thisMonthLeaves[] = $case;
                                        @endphp
                                    @endif
                                @endif
                            @endforeach
                        @endif --}}
                        @if (!isset($isFiltered))
                            @include('SimpleWorkflowReportView::Core.Summary.process.partial.rest-of-leaves-of-each-user', ['users' => $users])
                        @endIf
                    </div>
                @endif

                {{-- @include('SimpleWorkflowReportView::Core.Summary.process.partial.today-leaves') --}}
            </div>
        </div>
    </div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

@section('script')
    <script>
        initial_view();
        $('#timeoff-report').DataTable({
            "pageLength": 50,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
        $('#hourly-leaves').DataTable({
            "order": [
                [4, "desc"],
                [5, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
        $('#daily-leaves').DataTable({
            "order": [
                [4, "desc"],
                [5, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection

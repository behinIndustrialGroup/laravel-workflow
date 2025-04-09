@extends('behin-layouts.app')

@section('title')
    خلاصه گزارش فرایند {{ $process->name }}
@endsection

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;
    use Morilog\Jalali\Jalalian;
    $today = Carbon::today();
    $todayShamsi = Jalalian::fromCarbon($today);
    $thisYear = $todayShamsi->getYear();
    $thisMonth = $todayShamsi->getMonth();
    $totalLeaves = $thisMonth * 20;
    $users = DB::table('users')->get();
    foreach($users as $user){
        $approvedLeaves = DB::table('wf_entity_timeoffs')
        ->select(
                DB::raw('COALESCE(SUM(CASE WHEN wf_entity_timeoffs.type = "ساعتی" THEN duration ELSE duration*8 END), 0) as total_leaves')
            )
            ->where('user', $user->id)
            ->where(function ($query) use ($thisYear) {
                $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
            })
            ->where('approved', 1)
            ->first()->total_leaves;
        $user->approvedLeaves = $approvedLeaves;
        $restLeaves = ($thisMonth * 20) - $approvedLeaves;
        $user->restLeaves = $restLeaves;
            
    }

    $monthlyLeaves = DB::table('users')
        ->leftJoin('wf_entity_timeoffs', function ($join) use ($thisYear) {
            $join
                ->on('users.id', '=', 'wf_entity_timeoffs.user')
                ->where(function ($query) use ($thisYear) {
                    $query->where('start_year', $thisYear)->orWhere('end_year', $thisYear);
                })
                ->where('approved', 1);
        })
        ->select(
            'users.id as user_id',
            'users.name as user_name',
            'wf_entity_timeoffs.start_year',
            'wf_entity_timeoffs.start_month',
            DB::raw(
                'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.approved = 1 THEN duration ELSE 0 END), 0) as approved_leaves',
            ),
            DB::raw(
                'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.approved = 0 THEN duration ELSE 0 END), 0) as pending_or_rejected_leaves',
            ),
            DB::raw(
                'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.type = "ساعتی" THEN duration ELSE duration*8 END), 0) as total_leaves',
            ),
        )
        ->groupBy('users.id', 'users.name', 'wf_entity_timeoffs.start_year')
        ->orderBy('wf_entity_timeoffs.start_year', 'desc')
        ->orderBy('wf_entity_timeoffs.start_month', 'desc')
        ->get();

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
                        @php
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
                        @endif
                        @if (!isset($isFiltered))
                            <div class="card-header text-center bg-success">گزارش ماهانه مرخصی کاربران
                                <a href="{{ route('simpleWorkflowReport.process.export', $process->id) }}">
                                    <button class="btn btn-primary btn-sm">{{ trans('fields.Excel') }}</button>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="timeoff-report">
                                        <thead>
                                            <tr>
                                                <th>شماره پرسنلی</th>
                                                <th>نام کاربر</th>
                                                <th>سال</th>
                                                <th>ماه</th>
                                                <th>مانده مرخصی</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                @if (!in_array($user->id, [ 43]))
                                                    <tr>
                                                        <td>{{ $user->number }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $thisYear }}</td>
                                                        <td>{{ $thisMonth }}</td>
                                                        <td>
                                                            @if (auth()->user()->access('تغییر مانده مرخصی ها'))
                                                                <form
                                                                    action="{{ route('simpleWorkflowReport.process.update', ['processId' => $process->id]) }}"
                                                                    method="POST" id="leave-form">
                                                                    @csrf
                                                                    <input type="hidden" name="userId" id=""
                                                                        value="{{ $user->id }}">
                                                                    <input type="hidden" name="restBySystem" id=""
                                                                        class="form-control"
                                                                        value="{{ round($user->restLeaves, 2) }}">
                                                                    <input type="text" name="restByUser" id=""
                                                                        value="{{ round($user->restLeaves, 2) }}">
                                                                    <input type="submit" value="ثبت" name=""
                                                                        class="btn btn-primary btn-sm">
                                                                </form>
                                                            @else
                                                                {{ round($user->restLeaves, 2) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a
                                                                href="?userId={{ $user->id }}&year={{ $thisYear }}&month={{ $thisMonth }}">
                                                                <button
                                                                    class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            {{-- @foreach ($monthlyLeaves as $leave)
                                                @if (!in_array($leave->user_id, [1, 43]))
                                                    <tr>
                                                        <td>{{ getUserInfo($leave->user_id)?->number }}</td>
                                                        <td>{{ getUserInfo($leave->user_id)?->name }}</td>
                                                        <td>{{ $leave->start_year }}</td>
                                                        <td>{{ $leave->start_month }}</td>
                                                        <td dir="ltr">
                                                            @if (auth()->user()->access('تغییر مانده مرخصی ها'))
                                                                <form
                                                                    action="{{ route('simpleWorkflowReport.process.update', ['processId' => $process->id]) }}"
                                                                    method="POST" id="leave-form">
                                                                    @csrf
                                                                    <input type="hidden" name="userId" id=""
                                                                        value="{{ $leave->user_id }}">
                                                                    <input type="hidden" name="restBySystem" id=""
                                                                        class="form-control"
                                                                        value="{{ round($totalLeaves - $leave->total_leaves, 2) }}">
                                                                    <input type="text" name="restByUser" id=""
                                                                        value="{{ round($totalLeaves - $leave->total_leaves, 2) }}">

                                                                    <input type="submit" value="ثبت" name=""
                                                                        id="">
                                                                </form>
                                                            @else
                                                                {{ round($totalLeaves - $leave->total_leaves, 2) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a
                                                                href="?userId={{ $leave->user_id }}&year={{ $thisYear }}&month={{ $thisMonth }}">
                                                                <button
                                                                    class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endIf
                    </div>
                @endif

                <div class="card">
                    <a
                        href="{{ route('simpleWorkflowReport.process.export2', ['processId' => $process->id, 'userId' => $_GET['userId'] ?? '']) }}">
                        <button class="btn btn-primary btn-sm">{{ trans('fields.Excel') }}</button>
                    </a>
                    <div class="card-header text-center bg-warning">
                        جدول مرخصی های ساعتی {{ $user->name ?? '' }}

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- جدول مرخصی‌های ساعتی --}}
                            <table class="table table-bordered" id="hourly-leaves">
                                <thead>
                                    <tr>
                                        <th class="d-none">شناسه</th>
                                        <th>شماره پرونده</th>
                                        <th>ایجاد کننده</th>
                                        <th>نوع مرخصی</th>
                                        <th>تاریخ شروع</th>
                                        <th>ساعت شروع</th>
                                        <th>ساعت پایان</th>
                                        <th>مدیر دپارتمان</th>
                                        <th>تایید مدیر دپارتمان</th>
                                        <th>اقدام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hourlyLeaves as $case)
                                        <tr>
                                            <td class="d-none">{{ $case->id }}</td>
                                            <td>{{ $case->number }}</td>
                                            <td>{{ $case->creator()?->name }}</td>
                                            <td>{{ $case->getVariable('timeoff_request_type') }}</td>
                                            <td>{{ $case->getVariable('timeoff_hourly_request_start_date') }}</td>
                                            <td>{{ $case->getVariable('timeoff_start_time') }}</td>
                                            <td>{{ $case->getVariable('timeoff_end_time') }}</td>
                                            <td>{{ getUserInfo($case->getVariable('department_manager'))?->name }}
                                            </td>
                                            <td>{{ $case->getVariable('user_department_manager_approval') }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('simpleWorkflowReport.summary-report.edit', ['summary_report' => $case->id]) }}">
                                                    <button
                                                        class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-center bg-warning">
                        جدول مرخصی های روزانه {{ $user->name ?? '' }}

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- جدول مرخصی‌های روزانه --}}
                            <table class="table table-bordered" id="daily-leaves">
                                <thead>
                                    <tr>
                                        <th class="d-none">شناسه</th>
                                        <th>شماره پرونده</th>
                                        <th>ایجاد کننده</th>
                                        <th>نوع مرخصی</th>
                                        <th>تاریخ شروع</th>
                                        <th>تاریخ پایان</th>
                                        <th>مدت مرخصی</th>
                                        <th>مدیر دپارتمان</th>
                                        <th>تایید مدیر دپارتمان</th>
                                        <th>اقدام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($thisMonthLeaves as $case)
                                        <tr>
                                            <td class="d-none">{{ $case->id }}</td>
                                            <td>{{ $case->number }}</td>
                                            <td>{{ $case->creator()?->name }}</td>
                                            <td>{{ $case->getVariable('timeoff_request_type') }}</td>
                                            <td>{{ $case->getVariable('timeoff_start_date') }}</td>
                                            <td>{{ $case->getVariable('timeoff_end_date') }}</td>
                                            <td>{{ $case->getVariable('timeoff_daily_request_duration') }}</td>
                                            <td>{{ getUserInfo($case->getVariable('department_manager'))?->name }}
                                            </td>
                                            <td>{{ $case->getVariable('user_department_manager_approval') }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('simpleWorkflowReport.summary-report.edit', ['summary_report' => $case->id]) }}">
                                                    <button
                                                        class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

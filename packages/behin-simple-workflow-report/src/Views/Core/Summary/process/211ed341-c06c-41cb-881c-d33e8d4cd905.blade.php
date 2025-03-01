@extends('behin-layouts.app')

@section('title')
    خلاصه گزارش فرایند {{ $process->name }}
@endsection

@php
    use Illuminate\Support\Facades\DB;

    $monthlyLeaves = DB::table('wf_entity_timeoffs')
        ->select(
            'user',
            'request_year',
            'request_month',
            DB::raw('SUM(CASE WHEN approved = 1 THEN duration ELSE 0 END) as approved_leaves'),
            DB::raw('SUM(CASE WHEN approved = 0 THEN duration ELSE 0 END) as pending_or_rejected_leaves'),
            DB::raw('SUM(duration) as total_leaves'),
        )
        ->groupBy('user', 'request_year', 'request_month')
        ->orderBy('request_year', 'desc')
        ->orderBy('request_month', 'desc')
        ->get();
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
                        <div class="card-header text-center bg-success">گزارش ماهانه مرخصی کاربران</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>شماره پرسنلی</th>
                                            <th>نام کاربر</th>
                                            <th>سال</th>
                                            <th>ماه</th>
                                            <th>مجموع تایید شده</th>
                                            <th>تایید نشده / در انتظار تایید</th>
                                            <th>مجموع مرخصی (ساعت)</th>
                                            <th>مانده مرخصی (ساعت)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthlyLeaves as $leave)
                                            <tr>
                                                <td>{{ getUserInfo($leave->user)?->number }}</td>
                                                <td>{{ getUserInfo($leave->user)?->name }}</td>
                                                <td>{{ $leave->request_year }}</td>
                                                <td>{{ $leave->request_month }}</td>
                                                <td>{{ round($leave->approved_leaves) }}</td>
                                                <td>{{ $leave->pending_or_rejected_leaves }}</td>
                                                <td>{{ round($leave->total_leaves) }}</td>
                                                <td>{{ round(240 - $leave->total_leaves) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header text-center bg-warning">لیست پرونده های فرآیند {{ $process->name }}</div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="draft-list">
                                <thead>
                                    <tr>
                                        {{-- <th>ردیف</th> --}}
                                        <th class="d-none">شناسه</th>
                                        <th>شماره پرونده</th>
                                        <th>ایجاد کننده</th>
                                        <th>نوع مرخصی</th>
                                        <th>تاریخ شروع(مخصوص مرخصی های ساعتی)</th>
                                        <th>ساعت شروع(مخصوص مرخصی های ساعتی)</th>
                                        <th>ساعت پایان(مخصوص مرخصی های ساعتی)</th>
                                        <th>تاریخ شروع(برای مرخصی های روزانه)</th>
                                        <th>تاریخ پایان(برای مرخصی های روزانه)</th>
                                        <th>مدت مرخصی (برای مرخصی های روزانه)</th>
                                        <th>مدیر دپارتمان</th>
                                        <th>تایید مدیر دپارتمان</th>
                                        <th>اقدام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($process->cases as $case)
                                        <tr>
                                            {{-- <td>{{ $loop->iteration }}</td> --}}
                                            <td class="d-none">{{ $case->id }}</td>
                                            <td>{{ $case->number }}</td>
                                            <td>{{ $case->creator()?->name }}</td>

                                            <td>{{ $case->getVariable('timeoff_request_type') }}</td>
                                            <td>{{ $case->getVariable('timeoff_start_time') }}</td>
                                            <td>{{ $case->getVariable('timeoff_end_time') }}</td>
                                            <td>{{ $case->getVariable('timeoff_start_date') }}</td>
                                            <td>{{ $case->getVariable('timeoff_start_date') }}</td>
                                            <td>{{ $case->getVariable('timeoff_end_date') }}</td>
                                            <td>{{ $case->getVariable('timeoff_daily_request_duration') }}</td>
                                            <td>{{ getUserInfo($case->getVariable('department_manager'))?->name }}</td>
                                            <td>{{ $case->getVariable('user_department_manager_approval') }}</td>
                                            <td><a
                                                    href="{{ route('simpleWorkflowReport.summary-report.edit', ['summary_report' => $case->id]) }}"><button
                                                        class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button></a>
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

@section('script')
    <script>
        initial_view();
        $('#draft-list').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
                className: 'btn btn-sm-default',
                attr: {
                    style: 'direction: ltr'
                }
            }],
            "order": [
                [1, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection

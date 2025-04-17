<div class="card">
    <a
        href="{{ route('simpleWorkflowReport.process.export2', ['processId' => $process->id, 'userId' => $_GET['userId'] ?? '']) }}">
        <button class="btn btn-primary btn-sm">{{ trans('fields.Excel') }}</button>
    </a>
    <div class="card-header text-center bg-warning">
        جدول مرخصی های ساعتی {{ $tableName ?? '' }}

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
        جدول مرخصی های روزانه {{ $tableName ?? '' }}

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
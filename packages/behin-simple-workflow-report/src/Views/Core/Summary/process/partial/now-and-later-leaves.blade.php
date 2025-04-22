<div class="card">
    <a
        href="{{ route('simpleWorkflowReport.userTimeoffs', ['userId' => $userId]) }}">
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
                        <th class="d-none">شماره پرونده</th>
                        <th>ایجاد کننده</th>
                        <th>نوع مرخصی</th>
                        <th> شروع</th>
                        <th> پایان</th>
                        <th>مدت مرخصی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items->where('type', 'ساعتی') as $row)
                        <tr>
                            <td class="d-none">{{ $row->id }}</td>
                            <td class="d-none">{{ $row->case_number }}</td>
                            <td>{{ $row->user()?->name }}</td>
                            <td>{{ $row->type }}</td>
                            <td dir="ltr">{{ toJalali((int)$row->start_timestamp)->format('Y-m-d H:i') }}</td>
                            <td dir="ltr">{{ toJalali((int)$row->end_timestamp)->format('Y-m-d H:i') }}</td>
                            <td>{{ ((int)$row->end_timestamp - (int)$row->start_timestamp) / 3600  }}</td>
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
                        <th class="d-none">شماره پرونده</th>
                        <th>ایجاد کننده</th>
                        <th>نوع مرخصی</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ پایان</th>
                        <th>مدت مرخصی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items->where('type', 'روزانه') as $row)
                        <tr>
                            <td class="d-none">{{ $row->id }}</td>
                            <td class="d-none">{{ $row->case_number }}</td>
                            <td>{{ $row->user()?->name }}</td>
                            <td>{{ $row->type }}</td>
                            <td>{{ toJalali((int)$row->start_timestamp)->format('Y-m-d') }}</td>
                            <td>{{ toJalali((int)$row->end_timestamp)->format('Y-m-d') }}</td>
                            <td>{{ ((int)$row->end_timestamp - (int)$row->start_timestamp) / 3600 +1 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
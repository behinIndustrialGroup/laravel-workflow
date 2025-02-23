@extends('behin-layouts.app')


@section('title')
    خلاصه گزارش فرایند {{ $process->name }}
@endsection


@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">لیست پرونده های فرآیند {{ $process->name }}</div>

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

@extends('behin-layouts.app')

@section('title')
    گزارش‌های گردش کار
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">گزارش‌های گردش کار</div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <div class="panel-heading">فیلتر</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select name="status" id="status" class="form-control">
                                            <option value="">وضعیت</option>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->value }}">{{ $status->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <select name="repairman" id="repairman" class="form-control">
                                            <option value="">تعمیرکار</option>
                                            @foreach ($repairmans as $repairman)
                                                <option value="{{ $repairman->value }}">{{ $repairman->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                </div>
                            </div>

                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="draft-list">
                                <thead>
                                    <tr>
                                        <th>ردیف</th>
                                        <th>شماره پرونده</th>
                                        <th>نام مشتری</th>
                                        <th>تاریخ پذیرش</th>
                                        <th>نام دستگاه</th>
                                        <th>تعمیرکار</th>
                                        <th>قیمت دریافتی</th>
                                        <th>آخرین وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vars as $var)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $var->case_number }}</td>
                                            <td>{{ $var->customer_fullname }}</td>
                                            <td>{{ $var->receive_date }}</td>
                                            <td>{{ $var->device_name }}</td>
                                            <td>{{ getUserInfo($var->repairman)?->name }}</td>
                                            <td>{{ $var->payment_amount }}</td>
                                            <td>{{ $var->last_status }}</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.report.edit', [ 'report' => $var->case_id ]) }}" target="_blank" class="btn btn-primary btn-sm">مشاهده گزارش</a>
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
        var table = $('#draft-list').DataTable({
            "order": [
                [2, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            "columns": [{
                    "searchable": false
                },
                {
                    "searchable": true
                },
                {
                    "searchable": true
                },
                {
                    "searchable": false
                },
                {
                    "searchable": false
                },
                {
                    "searchable": true
                },
                {
                    "searchable": false
                },
                {
                    "searchable": true
                },
                {
                    "searchable": false
                },
            ]
        });

        // $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        //     var column1Value = $('#status').val().toLowerCase();
        //     var column2Value = $('#repairman').val().toLowerCase();

        //     var col1 = data[5].toLowerCase(); // مقدار ستون اول
        //     var col2 = data[7].toLowerCase(); // مقدار ستون دوم

        //     // شرط: هر دو مقدار فیلتر اعمال شوند
        //     if (
        //         (column1Value === '' || col1.includes(column1Value)) &&
        //         (column2Value === '' || col2.includes(column2Value))
        //     ) {
        //         return true;
        //     }
        //     return false;
        // });
        $('#status').on('change', function() {
            table.search(this.value).draw();
            // table.draw();
        });
    </script>
@endsection

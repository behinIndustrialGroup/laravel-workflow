@extends('behin-layouts.app')


@section('content')
    <div class="row">
        @if (auth()->user()->access('منو >>کارتابل>>فرایند جدید'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ trans('پذیرش دستگاه') }}</h3>

                        <p>{{ trans('ثبت پذیرش دستگاه جدید') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('simpleWorkflow.process.startListView') }}" class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endauth
        @if (auth()->user()->access('منو >>کارتابل>>کارتابل'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ trans('کارتابل من') }}</h3>

                        <p>{{ trans('لیست پرونده هایی که باید انجام دهید') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('simpleWorkflow.inbox.index') }}" class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endauth

        <div class="col-sm-3 ">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ trans('لیست کارها') }}</h3>

                    <p>{{ trans('لیست کارهایی که ابلاغ شده است') }}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="{{ route('todoList.index') }}" class="small-box-footer">{{ trans('مشاهده') }} <i
                        class="fa fa-arrow-circle-left"></i></a>
            </div>
        </div>
        @if (auth()->user()->access('منو >>گزارشات کارتابل>>خلاصه'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ trans('گزارش پرونده ها') }}</h3>

                        <p>{{ trans('گزارش پرونده ها بر اساس وضعیت') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.summary-report.index') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif

        @if (auth()->user()->access('منو >>گزارشات کارتابل>>مالی'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ trans('گزارش مالی') }}</h3>

                        <p>{{ trans('گزارش مالی بر اساس وضعیت') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.fin-report.index') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif

        @if (auth()->user()->access('ثبت درخواست مرخصی'))
        <div class="col-sm-3 ">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ trans('ثبت مرخصی') }}</h3>

                    <p>{{ trans('از این قسمت میتوانید مرخصی خود را ثبت کنید') }}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="{{ route('simpleWorkflow.process.start', [
                        'taskId' => '7f62e4ce-a96e-419a-8972-358fd642f39b',
                        'inDraft' => 0,
                        'force' => 0,
                        'redirect' => true,
                        ]) }}" class="small-box-footer">{{ trans('ثبت') }} <i
                        class="fa fa-arrow-circle-left"></i></a>
            </div>
        </div>
    @endauth
        
    @endsection

    @section('script')
        {{-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            send_ajax_get_request(
                "{{ route('pmAdmin.api.numberOfCaseByLastStatus') }}",
                function(response) {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Last Status');
                    data.addColumn('number', 'Total Records');
                    console.log(data);

                    response.forEach(function(item) {
                        data.addRows([item.last_status, item.total_records])
                    })
                    console.log(data);



                    // Set chart options
                    var options = {
                        'title': 'Last Status Distribution',
                        'width': 600,
                        'height': 400,
                        'pieHole': 0.4, // Optional: To make it a Donut chart
                        'is3D': true // Optional: For a 3D Pie Chart
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                    chart.draw(data, options);
                }
            )



        }
    </script> --}}
    @endsection

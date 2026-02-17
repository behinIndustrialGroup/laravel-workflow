@extends('behin-layouts.app')

@php
    $disableBackBtn = true;
@endphp

@section('content')
    <div class="row">
        @if (auth()->user()->access('آیکون پذیرش دستگاه جدید '))
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
                    <a href="{{ route('simpleWorkflow.process.startListView') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i class="fa fa-arrow-circle-left"></i></a>
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
                    <a href="{{ route('simpleWorkflow.inbox.index') }}" class="small-box-footer">{{ trans('مشاهده') }}
                        <i class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endauth

        <div class="col-sm-3 ">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ trans('اطلاع رسانی') }}</h3>

                    <p>{{ trans('آخرین اطلاع رسانی ها') }}</p>
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

        @if (auth()->user()->access('ثبت درخواست مرخصی'))
            <div class="col-sm-3 "
                onclick="if(confirm('شروع؟')) { window.location='{{ route('simpleWorkflow.process.start', [
                    'taskId' => '7f62e4ce-a96e-419a-8972-358fd642f39b',
                    'inDraft' => 0,
                    'force' => 1,
                    'redirect' => true,
                ]) }}'; }">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ trans('ثبت مرخصی') }}</h3>

                        <p>{{ trans('مرخصی خود را ثبت کنید') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <span class="small-box-footer">{{ trans('ثبت') }} <i
                            class="fa fa-arrow-circle-left"></i></span>
                </div>
            </div>
        @endauth
        @if (auth()->user()->access('وضعیت کارهای منقضی شده'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ trans('کارهای منقضی') }}</h3>

                        <p>{{ trans('از این قسمت میتوانید وضعیت کارهای منقضی شده را مشاهده کنید') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.expired-tasks.index') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif
        @if (auth()->user()->access('لیست دستگاه های مپا سنتر'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ trans('مپا سنتر') }}</h3>

                        <p>{{ trans('از این قسمت میتوانید لیست دستگاه های مپا سنتر را مشاهده کنید') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.mapa-center.index') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif
        @if (auth()->user()->access('طرف حسابها'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ trans('طرف حسابها') }}</h3>

                        <p>{{ trans('از این قسمت میتوانید لیست طرف حسابها را مشاهده کنید') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.counter-party.index') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif
        @if (auth()->user()->access('امور متفرقه'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ trans('امور متفرقه') }}</h3>

                        <p>{{ trans('ثبت گزارش روزانه امور متفرقه') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('simpleWorkflow.process.start', [
                        'taskId' => '1021591c-b6cf-4cb3-bbd8-028c5ed49253',
                        'inDraft' => 0,
                        'force' => 0,
                        'redirect' => true,
                    ]) }}"
                        class="small-box-footer">{{ trans('ثبت') }} <i
                            class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif
        @if (access('آیکون شروع فرایند ورود و خروج کالا از نگهبانی'))
            <div class="col-sm-3 "
                onclick="if(confirm('شروع؟')) { window.location='{{ route('simpleWorkflow.process.start', [
                    'taskId' => '28d862dd-9268-47ad-bf6f-aa0520e6d3d2',
                    'inDraft' => 0,
                    'force' => 1,
                    'redirect' => true,
                ]) }}'; }">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ trans('ورود و خروج کالا') }}</h3>

                        <p>{{ trans('ورود و خروج کالا از نگهبانی') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <span class="small-box-footer">{{ trans('ثبت') }} <i
                            class="fa fa-arrow-circle-left"></i></span>
                </div>
            </div>
        @endauth
        @if (access('آیکون شروع فرایند ماموریت'))
            <div class="col-sm-3 "
                onclick="if(confirm('شروع؟')) { window.location='{{ route('simpleWorkflow.process.start', [
                    'taskId' => 'cf07ec23-686f-4a08-9cb5-5ea258c4b1a0',
                    'inDraft' => 0,
                    'force' => 1,
                    'redirect' => true,
                ]) }}'; }">
                <!-- small box -->
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ trans('مأموریت') }}</h3>

                        <p>{{ trans('مأموریت') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <span class="small-box-footer">{{ trans('ثبت') }} <i
                            class="fa fa-arrow-circle-left"></i></span>
                </div>
            </div>
        @endauth
        <div class="col-sm-3 ">
            <!-- small box -->
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ trans('انبار') }}</h3>

                    <p>{{ trans('کالای امانی، قطعات مصرفی و لیست خریدها') }}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="{{ route('admin.warehouseDashboard') }}"
                    class="small-box-footer">{{ trans('مشاهده') }} <i
                        class="fa fa-arrow-circle-left"></i></a>
            </div>
        </div>
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

@extends('behin-layouts.app')

@php
    $disableBackBtn = true;
@endphp

@section('content')
    <div class="row">
        
        <div class="col-sm-3 ">
            <!-- small box -->
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ trans('کالای امانی') }}</h3>

                    <p>{{ trans('ثبت و مدیریت کالاهای امانی') }}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="{{ route('simpleWorkflowReport.borrow-requests.index') }}"
                    class="small-box-footer">{{ trans('مشاهده') }} <i
                        class="fa fa-arrow-circle-left"></i></a>
            </div>
        </div>
        @if (access('درخواست قطعات مصرفی'))
            <div class="col-sm-3 "
                onclick="window.location='{{ route('simpleWorkflowReport.consumable-parts.index') }}'">
                <!-- small box -->
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ trans('درخواست قطعات مصرفی') }}</h3>

                        <p>{{ trans('درخواست قطعات مصرفی') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <span class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></span>
                </div>
            </div>
        @endauth
        @if (access('لیست خرید داخلی و خارجی'))
            <div class="col-sm-3 "
                onclick="window.location='{{ route('simpleWorkflowReport.consumable-parts.buyingList') }}'">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ trans('لیست خرید') }}</h3>

                        <p>{{ trans('لیست خرید داخلی و خارجی') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <span class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></span>
                </div>
            </div>
        @endauth
        @if (access('دریافت کالا از انبار'))
            <div class="col-sm-3 "
                onclick="window.location='{{ route('simpleWorkflowReport.consumable-parts.create') }}'">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ trans('دریافت کالا از انبار') }}</h3>

                        <p>{{ trans('ثبت درخواست دریافت کالای مصرفی از انبار') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <span class="small-box-footer">{{ trans('مشاهده') }} <i
                            class="fa fa-arrow-circle-left"></i></span>
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

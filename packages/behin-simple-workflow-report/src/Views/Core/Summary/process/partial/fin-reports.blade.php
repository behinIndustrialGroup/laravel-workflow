@extends('behin-layouts.app')


@section('title')
    گزارش مالی
@endsection

@section('content')
    <div class="row">
        @if (auth()->user()->access('منو >>گزارشات کارتابل>>مالی'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner"  style="height: 150px">
                        <h3>{{ trans('پرسنل') }}</h3>

                        <p>{{ trans('عملکرد مالی پرسنل') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.fin.totalCost') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif
        @if (auth()->user()->access('گزارش کل تعیین هزینه ها و دریافت هزینه ها'))
            <div class="col-sm-3 ">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner"  style="height: 150px">
                        <h3>{{ trans('دریافتی ها') }}</h3>

                        <p>{{ trans('گزارش کل تعیین هزینه ها و دریافت هزینه ها') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('simpleWorkflowReport.fin.allPayments') }}"
                        class="small-box-footer">{{ trans('مشاهده') }} <i class="fa fa-arrow-circle-left"></i></a>
                </div>
            </div>
        @endif
    </div>
@endsection

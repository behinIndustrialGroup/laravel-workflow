@extends('behin-layouts.app')


@section('title')
    گزارش مالی
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3>{{ trans('fields.Start Process') }}</h3>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="list-group">
                @if (auth()->user()->access('منو >>گزارشات کارتابل>>مالی'))
                    <a href="{{ route('simpleWorkflowReport.fin.totalCost') }}"
                        class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">پرسنل</h5>
                            <small class="btn btn-sm btn-primary">مشاهده</small>
                        </div>
                        <p class="mb-1"></p>
                        <small>عملکرد مالی پرسنل</small>
                    </a>
                @endif
                @if (auth()->user()->access('گزارش کل تعیین هزینه ها و دریافت هزینه ها'))

                    <a href="{{ route('simpleWorkflowReport.fin.allPayments') }}"
                        class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">هزینه ها</h5>
                            <small class="btn btn-sm btn-primary">مشاهده</small>
                        </div>
                        <p class="mb-1"></p>
                        <small>گزارش کامل تعیین هزینه ها و دریافت هزینه ها</small>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

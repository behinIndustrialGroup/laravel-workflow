@extends('behin-layouts.app')


@section('title')
    گزارش مالی
@endsection

@section('content')
    <div class="row col-sm-12">
        <div class="card col-sm-12">
            <div class="card-header">گزارش‌های مالی</div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="d-none">شناسه</th>
                                <th>عنوان گزارش</th>
                                <th class="d-none">توضیحات</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (auth()->user()->access('منو >>گزارشات کارتابل>>مالی'))
                                <tr>
                                    <td class="d-none"></td>
                                    <td>پرسنل</td>
                                    <td class="d-none">عملکرد مالی پرسنل</td>
                                    <td>
                                        <a href="{{ route('simpleWorkflowReport.fin.totalCost') }}"
                                            class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                    </td>
                                </tr>
                            @endif
                            @if (auth()->user()->access('گزارش کل تعیین هزینه ها و دریافت هزینه ها'))

                                <tr>
                                    <td class="d-none"></td>
                                    <td>هزینه ها</td>
                                    <td class="d-none">گزارش کامل تعیین هزینه ها و دریافت هزینه ها</td>
                                    <td>
                                        <a href="{{ route('simpleWorkflowReport.fin.allPayments') }}"
                                            class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

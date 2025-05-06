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
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="d-none">شناسه</th>
                                        <th>عنوان فرآیند</th>
                                        <th class="d-none">توضیحات</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($processes as $process)
                                        @if (auth()->user()->access('خلاصه گزارش فرایند: ' . $process->name))
                                            <tr>
                                                <td class="d-none">{{ $process->id }}</td>
                                                <td>{{ $process->name }}</td>
                                                <td class="d-none">{{ $process->description }}</td>
                                                <td>
                                                    <a href="{{ route('simpleWorkflowReport.summary-report.show', ['summary_report' => $process]) }}"
                                                        class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if (auth()->user()->access('امور جاری: گزارش فرایند های داخلی و خارجی'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>امور جاری</td>
                                            <td class="d-none">گزارش کامل فرایند های داخلی و خارجی</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.external-internal.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
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
        </div>
    </div>
@endsection

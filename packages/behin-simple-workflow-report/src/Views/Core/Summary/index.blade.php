@extends('behin-layouts.app')

@section('title')
    گزارش‌های گردش کار
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center bg-info">جستجو</div>
                    <div class="card-body">
                        <div>
                            <form action="javascript:void(0)" method="POST" id="search-form" class="row">
                                <div class="form-group col-sm-4">
                                    <label for="">هر چه میخواهد دل تنگت بجوی</label>
                                    <div class="input-group">
                                        <input type="text" name="q" id="" class="form-control" placeholder="شماره پرونده یا نام مشتری">
                                        <div class="input-group-append" onclick="search()" style="cursor: pointer">
                                            <div class="input-group-text">
                                                <span class="fa fa-search" ></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 table-responsive d-none" id="search-result">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>شماره پرونده</th>
                                                <th>نام مشتری</th>
                                                <th>آخرین وضعیت</th>
                                                <th>تاریخ ایجاد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                            <script>
                                function search () { 
                                    var fd = new FormData($('#search-form')[0])
                                    send_ajax_formdata_request(
                                        "{{ route('simpleWorkflowReport.external-internal.search') }}",
                                        fd,
                                        function(response){
                                            $('#search-result').removeClass('d-none')
                                            $('#search-result tbody').html(response)
                                        }
                                    )
                                 }
                            </script>
                        </div>
                    </div>
                </div>
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

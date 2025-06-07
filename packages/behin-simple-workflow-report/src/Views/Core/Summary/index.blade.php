@extends('behin-layouts.app')

@section('title')
    گزارش‌های گردش کار
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-12">
                @if(auth()->user()->access('هرچه میخواهد دل تنگت بجوی'))
                <div class="card">
                    <div class="card-header text-center bg-info">جستجو</div>
                    <div class="card-body">
                        <div>
                            <label for="">هر چه میخواهد دل تنگت بجوی</label>
                            <form action="javascript:void(0)" method="POST" id="search-form" class="row">
                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <select name="actor" id="" class="form-control" placeholder="کارشناس">
                                            <option value="">کارشناس</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <input type="text" name="customer" id="" class="form-control"
                                            placeholder="نام مشتری">
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <input type="text" name="number" id="" class="form-control"
                                            placeholder="شماره پرونده">
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <input type="text" name="mapa_serial" id="" class="form-control"
                                            placeholder="سریال مپا">
                                        <div class="input-group-append" onclick="search()" style="cursor: pointer;">
                                            <div class="input-group-text" style="background: #25a5c8">
                                                <span class="fa fa-search"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 table-responsive d-none" id="search-result">
                                    <table class="table table-bordered" id="search-result-table">
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
                                function search() {
                                    var fd = new FormData($('#search-form')[0])
                                    send_ajax_formdata_request(
                                        "{{ route('simpleWorkflowReport.external-internal.search') }}",
                                        fd,
                                        function(response) {
                                            console.log(response)
                                            $('#search-result').removeClass('d-none')

                                            if (response.length == 0) {
                                                $('#search-result').addClass('d-none')
                                            }
                                            $('#search-result-table').DataTable().destroy()
                                            $('#search-result tbody').html(response)
                                            $('#search-result-table').DataTable({
                                                "order": [
                                                    [3, "desc"]
                                                ],
                                                "language": {
                                                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                                                },
                                            })
                                        }
                                    )
                                }
                            </script>
                        </div>
                    </div>
                </div>
                @endif
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
                                    @if (auth()->user()->access('بایگانی: گزارش پرونده های بایگانی شده'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>بایگانی</td>
                                            <td class="d-none">گزارش کامل پرونده های بایگانی شده</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.external-internal-archive') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('مپا سنتر: پرونده های پایان یافته'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>مپاسنتر: پرونده های پایان یافته</td>
                                            <td class="d-none">گزارش کامل پرونده های پایان یافته مپاسنتر</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.mapa-center-archive') }}"
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

                                    @if (auth()->user()->access('گزارش چک ها'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>چک ها</td>
                                            <td class="d-none">گزارش کامل چک ها و تاریخ سررسید آنها</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.cheque-report.index') }}"
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

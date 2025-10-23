@extends('behin-layouts.app')

@section('title')
    گزارش‌های گردش کار
@endsection

@section('style')
    <style>
        .has-value {
            border: 2px solid #28a745 !important;
            /* سبز برای مثال */
            background-color: #e8f5e9;
            /* سبز روشن پس‌زمینه */
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-12">
                @if (auth()->user()->access('هرچه میخواهد دل تنگت بجوی'))
                    <div class="card">
                        <div class="card-header text-center bg-info">جستجو</div>
                        <div class="card-body">
                            <div>
                                <label for="">هر چه میخواهد دل تنگت بجوی</label>
                                <form action="javascript:void(0)" method="POST" id="search-form" class="row">
                                    <div class="form-group col-sm-3">
                                        <div class="input-group">
                                            <select name="actor" id="" class="form-control"
                                                placeholder="کارشناس">
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
                                            <input type="text" name="device_name" id="" class="form-control"
                                                placeholder="نام دستگاه">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <div class="input-group">
                                            <input type="text" name="mapa_serial" id="" class="form-control"
                                                placeholder="سریال مپا">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-3"></div>
                                    <div class="form-group col-sm-3"></div>
                                    <div class="form-group col-sm-3">
                                        <button class="btn btn-info" onclick="search()" style="width: 100%">
                                            جستجو
                                        </button>
                                    </div>
                                    <div class="col-sm-12 table-responsive d-none" id="search-result">
                                        <table class="table table-bordered" id="search-result-table">
                                            <thead>
                                                <tr>
                                                    <th>شماره پرونده</th>
                                                    <th>نام مشتری</th>
                                                    <th style='width: 70px'>نام دستگاه</th>
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
                                    $('#search-form').on('submit', function(e) {
                                        e.preventDefault(); // جلوگیری از رفتار پیش‌فرض ارسال فرم
                                        search(); // اجرای تابع جستجو
                                    });

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
                                <script>
                                    $(document).ready(function() {
                                        // تمام فیلدهای ورودی و سلکت‌ها را بررسی کن
                                        $('#search-form input, #search-form select').each(function() {
                                            toggleHasValueClass($(this)); // در ابتدا بررسی کن

                                            // در زمان تغییر یا تایپ، بررسی مجدد کن
                                            $(this).on('input change', function() {
                                                toggleHasValueClass($(this));
                                            });
                                        });

                                        function toggleHasValueClass($el) {
                                            if ($el.val()) {
                                                $el.addClass('has-value');
                                            } else {
                                                $el.removeClass('has-value');
                                            }
                                        }
                                    });
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
                                    @if (auth()->user()->access('پرونده های حذف شده'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>پرونده های حذف شده</td>
                                            <td class="d-none"></td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.summary-report.show', ['summary_report' => '243225f2-30b9-4fd7-abc7-fbcf97862a1b']) }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('دفتر تلفن'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>دفتر تلفن</td>
                                            <td class="d-none"></td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.phonebook.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
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
                                    @if (auth()->user()->access('گزارش درآمد تقریبی'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش درآمد(هزینه و پرسنل)</td>
                                            <td class="d-none">گزارش درآمد </td>
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
                                            <td>دفتر چک ها</td>
                                            <td class="d-none">گزارش کامل چک ها و تاریخ سررسید آنها</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.cheque-report.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('گزارش حساب دفتری'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>حساب دفتری</td>
                                            <td class="d-none">گزارش کامل حساب دفتری</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.on-credit-report.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('گزارش بدهکاران'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>بدهکاران</td>
                                            <td class="d-none">گزارش کامل بدهکاران</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.on-credit-report.showAll') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif

                                    @if (auth()->user()->access('گزارش اقدامات پرسنل'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>اقدامات پرسنل</td>
                                            <td class="d-none">گزارش کامل</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.personel-activity.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('گزارش روزانه'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش روزانه پرسنل</td>
                                            <td class="d-none">گزارش کامل</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.daily-report.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('گزارش جرایم پیامک ها'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش جرایم و پاداش ها</td>
                                            <td class="d-none">گزارش کامل</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.daily-report.reminder-summary') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('گزارش لیست طلبکاران'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش لیست طلبکاران</td>
                                            <td class="d-none">گزارش کامل لیست طلبکاران</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.creditor.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (auth()->user()->access('گزارش تنخواه'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش ریز خرج کرد</td>
                                            <td class="d-none">گزارش تنخواه</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.petty-cash.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (access('گزارش ورود کالاها در نگهبانی'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش ورود کالاها در نگهبانی</td>
                                            <td class="d-none">گزارش کامل ورود کالاها در نگهبانی</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.goods-in.index') }}"
                                                    class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (access('گزارش ماموریت'))
                                        <tr>
                                            <td class="d-none"></td>
                                            <td>گزارش ماموریت</td>
                                            <td class="d-none">گزارش کامل ماموریت</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.missions.index') }}"
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

@extends('behin-layouts.app')

@section('title')
    مپا سنتر
@endsection

@php
use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
    $disableBackBtn = true;
    $installPartViewModelId = "218c2926-67c3-4344-a54d-5f718eba3882";
    $installPartViewModel = ViewModelController::getById($installPartViewModelId);
    $installPartViewModelUpdateForm = $installPartViewModel->update_form;
    $installPartViewModelApikey = $installPartViewModel->api_key;
@endphp

@php
    if(auth()->user()->access('مپاسنتر: امکان ویرایش اطلاعات مشتری')){
        $customerForm = getFormInformation('d6a98160-91aa-4f17-9bb3-f9284b2882b2');
    }else{
        $customerForm = getFormInformation('ac67f40d-4aa1-417a-9284-6a5c2f571ea1');
    }
    $deviceForm = getFormInformation('670fb05c-a794-4677-be5d-80b6c9b13da9');
    $fixForm = getFormInformation('14a68757-f609-44e1-82e9-4dc5ac35d60e');
    $variables = $case->variables();
@endphp

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="javascript:history.back()" class="btn btn-outline-primary float-left">
                <i class="fa fa-arrow-left"></i> {{ trans('fields.Back') }}
            </a>
        </div>
    </div>
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">مپا سنتر</h3>
            شماره پرونده: {{ $case->number }}
        </div>
        <div class="card-body">
            <div class="card">
                <form action="{{ route('simpleWorkflowReport.mapa-center.update-case-info', $case->id) }}" method="POST"
                    onsubmit="return confirm('آیا از اینکه اطلاعات را ویرایش میکنید مطمئن هستید؟')">
                    @csrf
                    @method('PUT')
                    @include('SimpleWorkflowView::Core.Form.preview', [
                        'form' => $customerForm,
                        'case' => $case,
                        'variables' => $variables,
                    ])
                    @if (auth()->user()->access('مپاسنتر: امکان ویرایش اطلاعات مشتری'))
                        <input type="submit" value="ویرایش" class="btn btn-primary m-2">
                    @endif
                </form>
            </div>

            <div class="card">
                <form action="{{ route('simpleWorkflowReport.mapa-center.update-case-info', $case->id) }}" method="POST"
                    onsubmit="return confirm('آیا از اینکه اطلاعات را ویرایش میکنید مطمئن هستید؟')">
                    @csrf
                    @method('PUT')
                    @include('SimpleWorkflowView::Core.Form.preview', [
                        'form' => $deviceForm,
                        'case' => $case,
                        'variables' => $variables,
                    ])
                    @if (auth()->user()->access('مپاسنتر: امکان ویرایش اطلاعات دستگاه'))
                        <input type="submit" value="ویرایش" class="btn btn-primary m-2">
                    @endif
                </form>
            </div>

            <div class="card">
                <div class="card-header bg-info text-center">
                    قطعات تعمیر شده
                </div>
                <div class="card-body">
                    <div class="col-sm-12">
                        <div class="row table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>قطعه</th>
                                    <th>سرپرست</th>
                                    <th>تعمیرکار</th>
                                    <th>سریال مپا</th>
                                    <th>واحد</th>
                                    <th>گزارش</th>
                                    <th>تایید تعمیرات</th>
                                    <th>تصویر</th>
                                    <th>اعزام کارشناس</th>
                                    <th>کارشناس اعزام شده</th>
                                    <th>توضیحات اعزام کارشناس</th>
                                    <th>تاریخ پایان کار</th>
                                    <th>ساعت پایان کار</th>
                                    <th>مدت تعمیرات</th>
                                    <th>{{ trans('fields.see_the_problem') }}</th>
                                    <th>{{ trans('fields.final_result_and_test') }}</th>
                                    <th>{{ trans('fields.test_possibility') }}</th>
                                    <th>{{ trans('fields.final_result') }}</th>
                                    <th>{{ trans('fields.problem_seeing') }}</th>
                                    <th>{{ trans('fields.sending_for_test_and_troubleshoot') }}</th>
                                    <th>{{ trans('fields.test_in_another_place') }}</th>
                                    <th>{{ trans('fields.job_rank') }}</th>
                                    <th>{{ trans('fields.other_parts') }}</th>
                                    <th>{{ trans('fields.special_parts') }}</th>
                                    <th>{{ trans('fields.power') }}</th>
                                    <th>{{ trans('fields.has_attachment') }}</th>
                                    <th>{{ trans('fields.attachment_image') }}</th>
                                </tr>
                                @foreach ($parts as $part)
                                    <tr>
                                        <td>{{ $part->name }}</td>
                                        <td>{{ getUserInfo($part->mapa_expert_head)->name ?? '' }}</td>
                                        <td>{{ getUserInfo($part->mapa_expert)->name ?? '' }}</td>
                                        <td>{{ $part->mapa_serial }}</td>
                                        <td>{{ $part->refer_to_unit }}</td>
                                        <td>{{ $part->fix_report }}</td>
                                        <td>{{ $part->repair_is_approved }}</td>
                                        <td>
                                            @if ($part->initial_part_pic)
                                                <a href="{{ url("public/$part->initial_part_pic") }}" download>دانلود</a>
                                            @endif
                                        </td>
                                        <td>{{ $part->dispatched_expert_needed }}</td>
                                        <td>{{ $part->dispatched_expert }}</td>
                                        <td>{{ $part->dispatched_expert_description }}</td>
                                        <td>{{ $part->doneAt ? toJalali($part->doneAt)->format('Y-m-d') : '' }}</td>
                                        <td>{{ $part->doneAt ? toJalali($part->doneAt)->format('H:i') : '' }}</td>
                                        <td>{{ $part->repair_duration }}</td>
                                        <td>{{ $part->see_the_problem }}</td>
                                        <td>{{ $part->final_result_and_test }}</td>
                                        <td>{{ $part->test_possibility }}</td>
                                        <td>{{ $part->final_result }}</td>
                                        <td>{{ $part->problem_seeing }}</td>
                                        <td>{{ $part->sending_for_test_and_troubleshoot }}</td>
                                        <td>{{ $part->test_in_another_place }}</td>
                                        <td>{{ $part->job_rank }}</td>
                                        <td>{{ $part->other_parts }}</td>
                                        <td>{{ $part->special_parts }}</td>
                                        <td>{{ $part->power }}</td>
                                        <td>{{ $part->has_attachment }}</td>
                                        <td>
                                            @if ($part->attachment_image)
                                                <a href="{{ url("public/$part->attachment_image") }}" download>دانلود</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-info text-center">
                    گزارشات تعمیر
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-stripped" id="mapa-center-reports">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ</th>
                                <th>ساعت شروع</th>
                                <th>ساعت پایان</th>
                                <th>مدت زمان صرف شده(ساعت)</th>
                                <th>تکنسین</th>
                                <th>گزارش</th>
                                <th>اقدام</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{ $totalDuration = 0 }}
                            @foreach ($reports as $report)
                                @php
                                    $duration = round(((int) $report->end - (int) $report->start) / 3600, 2);
                                    $totalDuration += $duration;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td dir="ltr">{{ toJalali((int) $report->start)->format('Y-m-d') }}</td>
                                    <td dir="ltr">{{ toJalali((int) $report->start)->format('H:i') }}</td>
                                    <td dir="ltr">{{ toJalali((int) $report->end)->format('H:i') }}</td>
                                    <td>{{ $duration }}</td>
                                    <td>{{ getUserInfo($report->expert)?->name }}</td>
                                    <td>{{ $report->report }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="deleteReport('{{ $report->id }}')"><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-success">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>مجموع</td>
                                <td>{{ $totalDuration }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card ">
                <div class="card-header bg-info text-center">
                    خارج کردن قطعه از دستگاه
                </div>
                <div class="card-body">
                    <p>چنانچه قطعه ای از دستگاه خارج کردید با کلیک بر روی دکمه زیر نام قطعه را وارد کنید و ادامه مراحل در
                        فرایند داخلی طی خواهد شد</p>
                    <button class="btn btn-sm btn-danger" onclick="excludeDevice()">خارج کردن قطعه از دستگاه</button>
                    <form class="m-2" action="{{ route('simpleWorkflowReport.mapa-center.exclude-device', $case->id) }}"
                        method="POST" id="excludeDeviceForm" style="display: none;">
                        @csrf
                        <input type="text" name="part_name" class="form-control" placeholder="نام قطعه">
                        <input type="submit" value="ثبت" class="btn btn-primary">
                    </form>
                    <script>
                        function excludeDevice() {
                            if ($('#excludeDeviceForm').is(':visible')) {
                                $('#excludeDeviceForm').hide();
                            } else {
                                $('#excludeDeviceForm').show();
                            }
                        }
                    </script>
                </div>
            </div>

            <!-- باکس افزودن قطعه -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-center">نصب قطعه روی دستگاه</div>
                <div class="card-body">
                    <form action="{{ route('simpleWorkflowReport.mapa-center.install-part', $case->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <input type="text" name="part_name" class="form-control" placeholder="نام قطعه" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="part_value" class="form-control" placeholder="مقدار" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">ذخیره</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- جدول قطعات نصب‌شده -->
            <div class="card">
                <div class="card-header bg-warning text-center">لیست قطعات نصب‌شده</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>نام قطعه</th>
                                <th>مقدار</th>
                                <th>تاریخ ثبت</th>
                                @if (auth()->user()->access('مپاسنتر: نمایش جزئیات فاکتور قطعات نصب شده'))
                                    <th>منبع تامین</th>
                                    <th>شماره فاکتور</th>
                                    <th>تاریخ فاکتور</th>
                                    <th>مبلغ</th>
                                @endif
                                <th>حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $colspan = 4;
                                if (auth()->user()->access('مپاسنتر: نمایش جزئیات فاکتور قطعات نصب شده')) {
                                    $colspan = 8;
                                }
                                $totalAmount = 0;
                            @endphp
                            @forelse ($installParts as $part)
                                <tr>
                                    <td>{{ $part->name }}</td>
                                    <td>{{ $part->value }}</td>
                                    <td>{{ jdate($part->created_at)->format('Y/m/d') }}</td>
                                    @if (auth()->user()->access('مپاسنتر: نمایش جزئیات فاکتور قطعات نصب شده'))
                                        @php
                                            $totalAmount += (int)str_replace(',', '', $part->amount);
                                        @endphp
                                        <td>{{ $part->supply_source }}</td>
                                        <td>{{ $part->invoice_number }}</td>
                                        <td>{{ $part->invoice_date }}</td>
                                        <td>{{ $part->amount }}</td>
                                    @endif
                                    <td>
                                        @if (auth()->user()->access('مپاسنتر: امکان ویرایش قطعات نصب شده'))
                                            <i class="btn btn-sm btn-primary fa fa-edit"
                                                onclick="open_view_model_form(`{{ $installPartViewModelUpdateForm }}`, `{{ $installPartViewModelId }}`, `{{ $part->id }}`, `{{ $installPartViewModelApikey }}`)"></i>
                                        @endif
                                        @if (auth()->user()->access('مپاسنتر: امکان حذف قطعات نصب شده'))
                                            <a href="{{ route('simpleWorkflowReport.mapa-center.delete-install-part', $part->id) }}"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('آیا از حذف قطعه {{ $part->name }} مطمئن هستید؟')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $colspan }}" class="text-center">قطعه‌ای ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(auth()->user()->access('مپاسنتر: نمایش جزئیات فاکتور قطعات نصب شده'))
                        <tfoot>
                            <tr>
                                <td colspan="{{ $colspan - 2 }}" class="text-center">مجموع</td>
                                <td>{{ number_format($totalAmount) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>



            @if (auth()->user()->access('گزارش مالی مپا سنتر'))
                <div class="card">
                    <div class="card-header bg-info text-center">گزارش مالی</div>
                    <div class="card-body row table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{ trans('fields.process_name') }}</th>
                                <th>{{ trans('fields.fix_cost_type') }}</th>
                                <th>{{ trans('fields.fix_cost_date') }}</th>
                                <th>{{ trans('fields.cost') }}</th>
                                <th>{{ trans('fields.destination_account') }}</th>
                                <th>{{ trans('fields.destination_account_name') }}</th>
                                <th>{{ trans('fields.payment') }}</th>
                                <th>{{ trans('fields.payment_date') }}</th>
                                <th>{{ trans('fields.payment_after_completion') }}</th>
                            </tr>
                            @foreach ($financials as $fin)
                                <tr>
                                    <td>{{ $fin->process_name }}</td>
                                    <td>{{ $fin->fix_cost_type }}</td>
                                    <td>{{ $fin->fix_cost_date ? toJalali((int) $fin->fix_cost_date)->format('Y-m-d') : '' }}
                                    </td>
                                    <td>{{ number_format($fin->cost) }}
                                        @if ($fin->cost2)
                                            <br>
                                            {{ number_format($fin->cost2) }}
                                        @endif
                                        @if ($fin->cost3)
                                            <br>
                                            {{ number_format($fin->cost3) }}
                                        @endif
                                    </td>
                                    <td>{{ $fin->destination_account }}
                                        @if ($fin->destination_account_2)
                                            <br>
                                            {{ $fin->destination_account_2 }}
                                        @endif
                                        @if ($fin->destination_account_3)
                                            <br>
                                            {{ $fin->destination_account_3 }}
                                        @endif
                                    </td>
                                    <td>{{ $fin->destination_account_name }}
                                        @if ($fin->destination_account_name_2)
                                            <br>
                                            {{ $fin->destination_account_name_2 }}
                                        @endif
                                        @if ($fin->destination_account_name_3)
                                            <br>
                                            {{ $fin->destination_account_name_3 }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($fin->payment) }}
                                    </td>
                                    <td>{{ $fin->payment_date ? toJalali((int) $fin->payment_date)->format('Y-m-d') : '' }}
                                    </td>
                                    <td>{{ $fin->payment_after_completion }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif

            <div class="card">
                <form action="{{ route('simpleWorkflowReport.mapa-center.update', $case->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('SimpleWorkflowView::Core.Form.preview', [
                        'form' => $fixForm,
                        'case' => $case,
                    ])
                    <input type="submit" value="ذخیره" class="btn btn-primary m-2">
                </form>
            </div>
            @if (auth()->user()->access('ثبت پایان کار مپا سنتر'))
                <div class="card row">
                    <div class="card-header bg-info">
                        پایان کار
                    </div>
                    <div class="card-body row">
                        <div class="col-sm-4 mt-2">
                            <button class="btn btn-warning" onclick="sendForOnAccountPayment()">ارسال برای دریافت هزینه
                                علی
                                الحساب</button>
                        </div>
                        <div class="col-sm-4 mt-2">
                            <button class="btn btn-danger" onclick="sendForFixPrice()">ثبت پایان تعمیرات</button>
                        </div>
                    </div>
                    <script>
                        function sendForFixPrice() {
                            if (confirm('آیا از ثبت پایان تعمیرات مطمئن هستید؟')) {
                                var scriptId = "7ac4388d-c783-4ac2-8f9b-0bb01bee5818";
                                var fd = new FormData();
                                fd.append('caseId', '{{ $case->id }}')
                                runScript(scriptId, fd, function(response) {
                                    alert('خسته نباشید')
                                    window.location.href = '{{ route('simpleWorkflowReport.mapa-center.index') }}';
                                })
                            }
                        }

                        function sendForOnAccountPayment() {
                            if (confirm('آیا از ارسال برای دریافت هزینه علی الحساب مطمئن هستید؟')) {
                                var scriptId = "3861c846-4735-443e-bbdf-aa502f44239a";
                                var fd = new FormData();
                                fd.append('caseId', '{{ $case->id }}')
                                runScript(scriptId, fd, function(response) {
                                    show_message('ارسال شد برای دریافت هزینه علی الحساب')
                                    show_message('چند لحظه منتظر بمانید')

                                    console.log(response)

                                    // صبر کن 3 ثانیه بعد ریدایرکت کن
                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);
                                })
                            }
                        }
                    </script>
                </div>
            @endif
        </div>

    </div>
@endsection

@section('script')
    <script>
        initial_view()
    </script>
    <script>
        $(document).ready(function() {
            $('#mapa-center-reports').DataTable({
                'language': {
                    'url': 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fa.json'
                },
                'order': [
                    [1, 'desc']
                ],
            });
        });

        function deleteReport(id) {
            var scriptId = "05a8fc79-b957-441b-8de4-275d7893c827";
            var fd = new FormData();
            fd.append('reportId', id)
            if (confirm("آیا از حذف این گزارش مطمئن هستید؟")) {
                runScript(scriptId, fd, function(response) {
                    alert(response)
                    window.location.reload();
                })
            }
        }
    </script>
@endsection

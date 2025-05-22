@extends('behin-layouts.app')

@section('title')
    مپا سنتر
@endsection

@php
    $customerForm = getFormInformation('d6a98160-91aa-4f17-9bb3-f9284b2882b2');
    $deviceForm = getFormInformation('670fb05c-a794-4677-be5d-80b6c9b13da9');
    $fixForm = getFormInformation('14a68757-f609-44e1-82e9-4dc5ac35d60e');
    $variables = $case->variables();
@endphp

@section('content')
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
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">مپا سنتر</h3>
        </div>
        <div class="card-body">
            <div class="card">
                @include('SimpleWorkflowView::Core.Form.preview', [
                    'form' => $customerForm,
                    'case' => $case,
                    'variables' => $variables,
                ])
            </div>
            
            <div class="card">
                @include('SimpleWorkflowView::Core.Form.preview', [
                    'form' => $deviceForm,
                    'case' => $case,
                    'variables' => $variables,
                ])
            </div>
            
            <div class="card">
                <div class="card-header bg-info text-center">
                    قطعات جدا شده
                </div>
                <div class="card-body">
                    @foreach ($parts as $part)
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
                                                @if($part->initial_part_pic)
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
                                                @if($part->attachment_image)
                                                    <a href="{{ url("public/$part->attachment_image") }}" download>دانلود</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endforeach
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
                                        <button class="btn btn-sm btn-danger" onclick="deleteReport('{{ $report->id }}')"><i class="fa fa-trash"></i></button>
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
                    خارج کردن دستگاه
                </div>
                <div class="card-body">
                    <button class="btn btn-sm btn-danger" onclick="excludeDevice()">خارج کردن دستگاه</button>
                    <form class="m-2" action="{{ route('simpleWorkflowReport.mapa-center.exclude-device', $case->id) }}" method="POST" id="excludeDeviceForm" style="display: none;">
                        @csrf
                        <input type="text" name="part_name" class="form-control" placeholder="نام قطعه">
                        <input type="submit" value="ثبت" class="btn btn-primary">
                    </form>
                    <script>
                        function excludeDevice() {
                            if($('#excludeDeviceForm').is(':visible')){
                                $('#excludeDeviceForm').hide();
                            }else{
                                $('#excludeDeviceForm').show();
                            }
                        }
                    </script>
                </div>
            </div>



            
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
            <div class="card">
                @if (auth()->user()->access('ثبت پایان کار مپا سنتر'))
                    <div class="col-sm-12 text-center bg-info p-2">
                        پایان کار
                    </div>
                    <div class="col-sm-4 mt-2">
                        <button class="btn btn-info" onclick="sendForFixPrice()">ثبت پایان تعمیرات</button>
                    </div>
                    <script>
                        function sendForFixPrice() {
                            var scriptId = "7ac4388d-c783-4ac2-8f9b-0bb01bee5818";
                            var fd = new FormData();
                            fd.append('caseId', '{{ $case->id }}')
                            runScript(scriptId, fd, function(response) {
                                show_message('ارسال شد برای تعیین هزینه')
                                show_message('چند لحظه منتظر بمانید')

                                console.log(response)

                                // صبر کن 3 ثانیه بعد ریدایرکت کن
                                setTimeout(function() {
                                    window.location.href = '{{ route('simpleWorkflowReport.mapa-center.index') }}';
                                }, 3000);
                            })
                        }
                    </script>
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
                    'order': [[1, 'desc']],
                });
            });

            function deleteReport(id) {
                var scriptId = "05a8fc79-b957-441b-8de4-275d7893c827";
                var fd = new FormData();
                fd.append('reportId', id)
                if(confirm("آیا از حذف این گزارش مطمئن هستید؟")){
                    runScript(scriptId, fd, function(response) {
                        alert(response)
                        window.location.reload();
                    })
                }
            }
        </script>
    @endsection

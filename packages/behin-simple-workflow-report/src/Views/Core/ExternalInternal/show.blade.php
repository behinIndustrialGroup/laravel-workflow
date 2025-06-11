@extends('behin-layouts.app')

@section('title')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body table-responsive">
                {{-- <pre>
                    {{ print_r($customer) }}
                    {{ print_r($devices) }}
                    {{ print_r($deviceRepairReports) }}
                    {{ print_r($parts) }}
                    {{ print_r($financials) }}
                    {{ print_r($delivery) }}

                </pre> --}}
                <div class="card">
                    <div class="card-header bg-success">پذیرش</div>
                    <div class="card-body">
                        <div class="row table-responsive" id="admision">
                            <table class="table table-bordered">
                                <tr>
                                    <td>شماره پرونده: {{ $mainCase->number }}</td>
                                    <td>شروع پذیرش: {{ $mainCase->process->name }}</td>
                                    <td>پذیرش کننده: {{ $mainCase->creator()->name }}</td>
                                    <td>تاریخ پذیرش: <span
                                            dir="ltr">{{ toJalali($mainCase->created_at)->format('Y-m-d H:i') }}</span>
                                    </td>
                                    {{-- <td>دریافت کننده: {{ getUserInfo($mainCase->getVariable('receiver'))?->name }} </td> --}}
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-success">مشتری</div>
                    <div class="card-body">
                        <div class="row table-responsive" id="customer">
                            <table class="table table-bordered">
                                <tr>
                                    <td>نام مشتری: {{ $customer['name'] }}</td>
                                    <td>
                                        @if (auth()->user()->access('امور جاری - شماره مشتری'))
                                            موبایل مشتری: {{ $customer['mobile'] }}
                                        @endif
                                    </td>
                                    <td>شهر مشتری: {{ $customer['city'] }}</td>
                                    <td>آدرس مشتری: {{ $customer['address'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4">توضیحات اولیه:
                                        {{ $mainCase->getVariable('customer_init_description') }}<br>
                                        {{ $mainCase->getVariable('initial_description') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header {{ count($devices) ? 'bg-success' : 'bg-primary' }}">دستگاه</div>
                    <div class="card-body">
                        <div class="row table-responsive" id="devices">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>نام دستگاه</th>
                                        <th>مدل دستگاه</th>
                                        <th>سیستم کنترل دستگاه</th>
                                        <th>مدل سیستم کنترل دستگاه</th>
                                        <th>سریال مپا</th>
                                        <th>سریال دستگاه</th>
                                        <th>نقشه الکتریکی</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($devices as $device)
                                        <tr>
                                            <td>{{ $device->name }}</td>
                                            <td>{{ $device->model }}</td>
                                            <td>{{ $device->control_system }}</td>
                                            <td>{{ $device->control_system_model }}</td>
                                            <td>{{ $device->mapa_serial }}</td>
                                            <td>{{ $device->serial }}</td>
                                            <td>{{ $device->has_electrical_map }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header {{ count($deviceRepairReports) ? 'bg-success' : 'bg-primary' }}">گزارش فرایند
                        خارجی</div>
                    <div class="card-body">
                        <div class="row table-responsive" id="repair-reports">
                            <table class="table table-bordered">
                                <tr>
                                    <th>شروع</th>
                                    <th>پایان</th>
                                    <th>گزارش</th>
                                    <th>سرپرست</th>
                                    <th>تعمیرکار</th>
                                    <th>همکاران</th>
                                    <th>{{ trans('fields.need_next_visit') }}</th>
                                    <th>{{ trans('fields.next_visit_description') }}</th>
                                    <th>{{ trans('fields.part_left_from_customer_location') }}</th>
                                    <th>{{ trans('fields.was_backups_taken') }}</th>
                                    <th>{{ trans('fields.parameter_backup') }}</th>
                                    <th>{{ trans('fields.pcparam_backup') }}</th>
                                    <th>{{ trans('fields.sram_backup') }}</th>
                                    <th>{{ trans('fields.sysfile_backup') }}</th>
                                    <th>{{ trans('fields.prog_backup') }}</th>
                                    <th>{{ trans('fields.reason_of_not_taking_backup') }}</th>
                                    <th>{{ trans('fields.customer_validation_code') }}</th>
                                    <th>{{ trans('fields.customer_signature') }}</th>
                                    <th>{{ trans('fields.job_rank') }}</th>

                                </tr>
                                @foreach ($deviceRepairReports as $report)
                                    <tr>
                                        <td dir="ltr">{{ convertPersianToEnglish($report->start_date) }}
                                            {{ $report->start_time }}</td>
                                        <td dir="ltr">{{ convertPersianToEnglish($report->end_date) }}
                                            {{ $report->end_time }}</td>
                                        <td>{{ $report->report }}</td>
                                        <td>{{ getUserInfo($report->mapa_expert_head)->name ?? $report->mapa_expert_head }}
                                        </td>
                                        <td>{{ getUserInfo($report->mapa_expert)->name ?? $report->mapa_expert }}</td>
                                        <td>
                                            @php
                                                $companions = json_decode($report->mapa_expert_companions);
                                            @endphp
                                            @if (is_array($companions))
                                                @foreach ($companions as $companion)
                                                    {{ getUserInfo($companion)->name ?? $companion }}<br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $report->need_next_visit }}</td>
                                        <td>{{ $report->next_visit_description }}</td>
                                        <td>{{ $report->part_left_from_customer_location }}</td>
                                        <td>{{ $report->was_backups_taken }}</td>
                                        <td>{{ $report->parameter_backup }}</td>
                                        <td>{{ $report->pcparam_backup }}</td>
                                        <td>{{ $report->sram_backup }}</td>
                                        <td>{{ $report->sysfile_backup }}</td>
                                        <td>{{ $report->prog_backup }}</td>
                                        <td>{{ $report->reason_of_not_taking_backup }}</td>
                                        <td>{{ $report->customer_validation_code }}</td>
                                        <td>
                                            @if ($report->customer_signature)
                                                <a href="{{ $report->customer_signature }}" download>دانلود</a>
                                            @endif
                                        </td>
                                        <td>{{ $report->job_rank }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header {{ count($parts) ? 'bg-success' : 'bg-primary' }}">گزارش فرایند داخلی</div>
                    <div class="card-body">
                        <div class="row table-responsive" id="parts">
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
                                        <td>{{ $part->doneAt ? toJalali((int)$part->doneAt)->format('Y-m-d') : '' }}</td>
                                        <td>{{ $part->doneAt ? toJalali((int)$part->doneAt)->format('H:i') : '' }}</td>
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
                @if (count($mapaCenterReports))
                    <div class="card">
                        <div class="card-header bg-success text-center">
                            گزارشات مپاسنتر
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
                                    </tr>
                                </thead>
                                <tbody>
                                    {{ $totalDuration = 0 }}
                                    @foreach ($mapaCenterReports as $report)
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
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
                @if (auth()->user()->access('امور جاری - جزئیات مالی'))
                    <div class="card">
                        <div class="card-header {{ count($financials) ? 'bg-success' : 'bg-primary' }}">گزارش دریافتی مالی
                        </div>
                        <div class="card-body">
                            {{-- مالی --}}
                            <div class="row table-responsive" id="financials">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ trans('fields.process_name') }}</th>
                                        <th>{{ trans('fields.fix_cost_type') }}</th>
                                        <th>{{ trans('fields.fix_cost_date') }}</th>
                                        <th>هزینه اعلام شده</th>
                                        <th>{{ trans('fields.destination_account') }}</th>
                                        <th>{{ trans('fields.destination_account_name') }}</th>
                                        <th>هزینه دریافت شده</th>
                                        <th>{{ trans('fields.payment_date') }}</th>
                                        <th>{{ trans('fields.payment_after_completion') }}</th>
                                        <th>{{ trans('fields.description') }}</th>
                                    </tr>
                                    @foreach ($financials as $fin)
                                        <tr>
                                            <td>{{ $fin->process_name }}</td>
                                            <td>{{ $fin->fix_cost_type }}</td>
                                            <td>{{ $fin->fix_cost_date ? toJalali((int) $fin->fix_cost_date)->format('Y-m-d') : '' }}
                                            </td>
                                            <td>{{ number_format((int)$fin->cost) }}
                                                @if ($fin->cost2)
                                                    <br>
                                                    {{ number_format((int)$fin->cost2) }}
                                                @endif
                                                @if ($fin->cost3)
                                                    <br>
                                                    {{ number_format((int)$fin->cost3) }}
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
                                            <td>{{ $fin->description }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header {{ $delivery['delivery_date'] ? 'bg-success' : 'bg-primary' }}">تحویل</div>
                    <div class="card-body">
                        {{-- تحویل --}}
                        <div class="row table-responsive" id="delivery">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ trans('fields.delivery_date') }}</th>
                                    <th>{{ trans('fields.delivered_to') }}</th>
                                    <th>{{ trans('fields.delivery_description') }}</th>
                                </tr>
                                <tr>
                                    <td>{{ $delivery['delivery_date'] ? toJalali((int) $delivery['delivery_date'])->format('Y-m-d') : '' }}
                                    </td>
                                    <td>{{ $delivery['delivered_to'] }}</td>
                                    <td>{{ $delivery['delivery_description'] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @section('script')
        @endsection

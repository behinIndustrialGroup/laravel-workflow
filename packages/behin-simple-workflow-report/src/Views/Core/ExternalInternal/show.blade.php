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
                    <div class="card-header bg-primary">مشتری</div>
                    <div class="card-body">
                        <div class="row table-responsive" id="customer">
                            <table class="table">
                                <tr>
                                    <td>نام مشتری: {{ $customer['name'] }}</td>
                            <td>موبایل مشتری: {{ $customer['mobile'] }}</td>
                            <td>شهر مشتری: {{ $customer['city'] }}</td>
                            <td>آدرس مشتری: {{ $customer['address'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary">دستگاه</div>
            <div class="card-body">
                <div class="row table-responsive" id="devices">
                    <table class="table">
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
            <div class="card-header bg-primary">گزارش فرایند خارجی</div>
            <div class="card-body">
                <div class="row table-responsive" id="repair-reports">
                    <table class="table">
                        <tr>
                            <th>شروع</th>
                            <th>پایان</th>
                            <th>گزارش</th>
                            <th>سرپرست</th>
                            <th>تعمیرکار</th>
                        </tr>
                        @foreach ($deviceRepairReports as $report)
                            <tr>
                                <td dir="ltr">{{ convertPersianToEnglish($report->start_date) }}  {{ $report->start_time }}</td>
                                <td dir="ltr">{{ convertPersianToEnglish($report->end_date) }}  {{ $report->end_time }}</td>
                                <td>{{ $report->report }}</td>
                                <td>{{ getUserInfo($report->mapa_expert_head)->name ?? $report->mapa_expert_head }}</td>
                                <td>{{ getUserInfo($report->mapa_expert)->name ?? $report->mapa_expert}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary">گزارش فرایند داخلی</div>
            <div class="card-body">
                <div class="row table-responsive" id="parts">
                    <table class="table">
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
                            <th>مدت تعمیرات</th>
                            <th>{{ trans('fields.see_the_problem') }}</th>
                            <th>{{ trans('fields.final_result_and_test') }}</th>
                            <th>{{ trans('fields.test_possibility') }}</th>
                            <th>{{ trans('fields.final_result') }}</th>
                            <th>{{ trans('fields.sending_for_test_and_troubleshoot') }}</th>
                            <th>{{ trans('fields.test_in_another_place') }}</th>
                            <th>{{ trans('fields.job_rank') }}</th>
                            <th>{{ trans('fields.other_parts') }}</th>
                            <th>{{ trans('fields.special_parts') }}</th>
                            <th>{{ trans('fields.power') }}</th>
                            <th>{{ trans('fields.has_attachment') }}</th>
                        </tr>
                        @foreach ($parts as $part)
                            <tr>
                                <td>{{ $part->name }}</td>
                                <td>{{ getUserInfo($part->mapa_expert_head)->name }}</td>
                                <td>{{ getUserInfo($part->mapa_expert)->name }}</td>
                                <td>{{ $part->mapa_serial }}</td>
                                <td>{{ $part->refer_to_unit }}</td>
                                <td>{{ $part->fix_report }}</td>
                                <td>{{ $part->repair_is_approved }}</td>
                                <td>{{ $part->initial_part_pic }}</td>
                                <td>{{ $part->dispatched_expert_needed }}</td>
                                <td>{{ $part->dispatched_expert }}</td>
                                <td>{{ $part->dispatched_expert_description }}</td>
                                <td>{{ $part->repair_duration }}</td>
                                <td>{{ $part->see_the_problem }}</td>
                                <td>{{ $part->final_result_and_test }}</td>
                                <td>{{ $part->test_possibility }}</td>
                                <td>{{ $part->final_result }}</td>
                                <td>{{ $part->problem_seeing }}</td>
                                <td>{{ $part->final_result }}</td>
                                <td>{{ $part->sending_for_test_and_troubleshoot }}</td>
                                <td>{{ $part->test_in_another_place }}</td>
                                <td>{{ $part->job_rank }}</td>
                                <td>{{ $part->other_parts }}</td>
                                <td>{{ $part->special_parts }}</td>
                                <td>{{ $part->power }}</td>
                                <td>{{ $part->has_attachment }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary">مالی</div>
            <div class="card-body">
                {{-- مالی --}}
                <div class="row table-responsive" id="financials">
                    <table class="table">
                        <tr>
                            <th>{{ trans('fields.process_name') }}</th>
                            <th>{{ trans('fields.cost') }}</th>
                            <th>{{ trans('fields.fix_cost_date') }}</th>
                            <th>{{ trans('fields.destination_account') }}</th>
                            <th>{{ trans('fields.destination_account_name') }}</th>
                            <th>{{ trans('fields.payment') }}</th>
                            <th>{{ trans('fields.payment_date') }}</th>
                            <th>{{ trans('fields.payment_after_completion') }}</th>
                        </tr>
                        @foreach ($financials as $fin)
                            <tr>
                                <td>{{ $fin->process_name }}</td>
                                <td>{{ number_format($fin->cost) }}</td>
                                <td>{{ $fin->fix_cost_date ? toJalali((int)$fin->fix_cost_date)->format('Y-m-d') : '' }}</td>
                                <td>{{ $fin->destination_account }}</td>
                                <td>{{ $fin->destination_account_name }}</td>
                                <td>{{ number_format($fin->payment) }}</td>
                                <td>{{ $fin->payment_date ? toJalali((int)$fin->payment_date)->format('Y-m-d'): '' }}</td>
                                <td>{{ $fin->payment_after_completion }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary">تحویل</div>
            <div class="card-body">
                {{-- تحویل --}}
                <div class="row table-responsive" id="delivery">
                    <table class="table">
                        <tr>
                            <th>{{ trans('fields.delivery_date') }}</th>
                            <th>{{ trans('fields.delivered_to') }}</th>
                            <th>{{ trans('fields.delivery_description') }}</th>
                        </tr>
                        <tr>
                            <td>{{  $delivery['delivery_date'] ? toJalali((int)$delivery['delivery_date'])->format('Y-m-d') : '' }}</td>
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

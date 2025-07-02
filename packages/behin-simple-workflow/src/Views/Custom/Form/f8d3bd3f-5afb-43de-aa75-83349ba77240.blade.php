@php
    use Behin\SimpleWorkflow\Models\Entities\Parts;

    $parts = Parts::where('case_number', $case->number)->get();
@endphp

<div class="card">
    <div class="card-header {{ count($parts) ? 'bg-success' : 'bg-primary' }}">گزارش فرایند داخلی</div>
    <div class="card-body">
        <div class="row table-responsive" id="parts">
            <table class="table table-bordered">
                <thead style="background-color: #e2f7a2">
                    <tr>
                        <th>قطعه</th>
                        <th>سریال</th>
                        <th>واحد</th>
                        <th>سرپرست</th>
                        <th>کارشناسان مپا (زمان کل)</th>
                        <th>تایید تعمیرات</th>
                        <th>تصویر</th>
                        <th>اعزام کارشناس</th>
                        <th>کارشناس اعزام شده</th>
                        <th>توضیحات اعزام کارشناس</th>
                        <th>{{ trans('fields.final_result_and_test') }}</th>
                        <th>{{ trans('fields.test_possibility') }}</th>
                        <th>{{ trans('fields.final_result') }}</th>
                        <th>{{ trans('fields.problem_seeing') }}</th>
                        <th>{{ trans('fields.sending_for_test_and_troubleshoot') }}</th>
                        <th>{{ trans('fields.test_in_another_place') }}</th>
                        <th>{{ trans('fields.job_rank') }}</th>
                        <th>{{ trans('fields.has_attachment') }}</th>
                        <th>{{ trans('fields.attachment_image') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parts as $part)
                        <tr class="part-header" onclick="toggleReports({{ $part->id }})">
                            <td>{{ $part->name }}</td>
                            <td>{{ $part->mapa_serial }}</td>
                            <td>{{ $part->refer_to_unit }}</td>
                            <td>{{ getUserInfo($part->mapa_expert_head)->name ?? '-' }}</td>
                            <td>
                                @foreach ($part->experts() as $expert)
                                    {{ getUserInfo($expert->registered_by)->name ?? $expert->registered_by }}
                                    ({{ $expert->total_duration }})<br>
                                @endforeach
                            </td>
                            <td>{{ $part->repair_is_approved }}</td>
                            <td>
                                @if ($part->initial_part_pic)
                                    <a href="{{ url("public/$part->initial_part_pic") }}" download>دانلود</a>
                                @endif
                            </td>
                            <td>{{ $part->dispatched_expert_needed }}</td>
                            <td>{{ $part->dispatched_expert }}</td>
                            <td>{{ $part->dispatched_expert_description }}</td>
                            <td>{{ $part->final_result_and_test }}</td>
                            <td>{{ $part->test_possibility }}</td>
                            <td>{{ $part->final_result }}</td>
                            <td>{{ $part->problem_seeing }}</td>
                            <td>{{ $part->sending_for_test_and_troubleshoot }}</td>
                            <td>{{ $part->test_in_another_place }}</td>
                            <td>{{ $part->job_rank }}</td>

                            <td>{{ $part->has_attachment }}</td>
                            <td>
                                @if ($part->attachment_image)
                                    <a href="{{ url("public/$part->attachment_image") }}" download>دانلود</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card">
            <div class="card-header text-center" style="background-color: #e2f7a2">
                گزارش تعمیرات روزانه داخلی
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>قطعه</th>
                            <th>کارشناس مپا</th>
                            <th>{{ trans('fields.done_at') }}</th>
                            <th>{{ trans('fields.repair_duration') }}</th>
                            <th colspan="6">{{ trans('fields.fix_report') }}</th>
                            <th>{{ trans('fields.see_the_problem') }}</th>
                            <th>{{ trans('fields.other_parts') }}</th>
                            <th>{{ trans('fields.special_parts') }}</th>
                            <th>{{ trans('fields.power') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parts as $part)
                            @foreach ($part->reports() as $report)
                                <tr>
                                    <td>{{ $part->name }}</td>
                                    <td>{{ getUserInfo($report->registered_by)->name ?? '-' }}</td>
                                    <td>{{ $report->done_at ? toJalali((int) $report->done_at)->format('Y-m-d') : '' }}
                                    </td>
                                    <td>{{ $report->repair_duration }}</td>
                                    <td colspan="6">{{ $report->fix_report }}</td>
                                    <td>{{ $report->see_the_problem }}</td>
                                    <td>{{ $report->other_parts }}</td>
                                    <td>{{ $report->special_parts }}</td>
                                    <td>{{ $report->power }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
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
                                <td dir="ltr">
                                    {{ toJalali((int) $report->start)->format('Y-m-d') }}</td>
                                <td dir="ltr">{{ toJalali((int) $report->start)->format('H:i') }}
                                </td>
                                <td dir="ltr">{{ toJalali((int) $report->end)->format('H:i') }}
                                </td>
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
            <div class="card-header {{ count($financials) ? 'bg-success' : 'bg-primary' }}">گزارش
                دریافتی مالی
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
                                <td>{{ number_format((int) $fin->cost) }}
                                    @if ($fin->cost2)
                                        <br>
                                        {{ number_format((int) $fin->cost2) }}
                                    @endif
                                    @if ($fin->cost3)
                                        <br>
                                        {{ number_format((int) $fin->cost3) }}
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
        <div class="card-header {{ $delivery['delivery_date'] ? 'bg-success' : 'bg-primary' }}">تحویل
        </div>
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

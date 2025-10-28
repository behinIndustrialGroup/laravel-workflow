@extends('behin-layouts.app')

@section('title', 'گزارش مأموریت‌ها')
@php
    use Morilog\Jalali\Jalalian;
@endphp

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <form class="row" method="GET">
                <div class="col-md-3">
                    <select name="month" id="month-filter" class="form-select form-control">
                        @foreach ($monthOptions as $option)
                            <option value="{{ $option['value'] }}" data-from="{{ $option['from'] }}" data-to="{{ $option['to'] }}" {{ $selectedMonth === $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="from_date" class="form-control persian-date" value="{{ $fromValue }}" placeholder="از تاریخ">
                </div>
                <div class="col-md-3">
                    <input type="text" name="to_date" class="form-control persian-date" value="{{ $toValue }}" placeholder="تا تاریخ">
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning" type="submit">فیلتر</button>
                        <a class="btn btn-success" href="{{ route('simpleWorkflowReport.missions.export', request()->query()) }}">
                            دریافت اکسل
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="missions-table">
                    <thead>
                        <tr>
                            <th>عنوان مأموریت</th>
                            <th>ایجادکننده</th>
                            <th>تاریخ شروع</th>
                            <th>تاریخ پایان</th>
                            <th>مدت (ساعت)</th>
                            <th>پرونده ها</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($missions as $mission)
                            <tr>
                                <td>{{ $mission->title }}</td>
                                <td>{{ getUserInfo($mission->created_by)->name ?? '-' }}</td>
                                <td dir="ltr">
                                    {{ $mission->start_datetime_carbon ? toJalali($mission->start_datetime_carbon)->format('Y-m-d H:i') : '' }}
                                </td>
                                <td dir="ltr">
                                    {{ $mission->end_datetime_carbon ? toJalali($mission->end_datetime_carbon)->format('Y-m-d H:i') : '' }}
                                </td>
                                <td dir="ltr">
                                    {{ $mission->duration_hours !== null ? number_format($mission->duration_hours, 2) : '-' }}
                                </td>
                                <td>
                                    @foreach ($mission->cases as $case)
                                        <i class="fa fa-external-link text-primary" onclick="window.open('{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $case->related_case_number]) }}', '_blank')"></i>
                                        {{ $case->related_case_number ?? '-' }}<br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#missions-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fa.json'
            },
            order: [[1, 'desc']],
            pageLength: 25
        });

        $(function() {
            const monthFilter = document.getElementById('month-filter');
            const fromInput = document.querySelector('input[name="from_date"]');
            const toInput = document.querySelector('input[name="to_date"]');

            function applySelectedMonth() {
                if (!monthFilter) {
                    return;
                }
                const selectedOption = monthFilter.options[monthFilter.selectedIndex];
                if (!selectedOption) {
                    return;
                }
                if (fromInput && selectedOption.dataset.from) {
                    fromInput.value = selectedOption.dataset.from;
                }
                if (toInput && selectedOption.dataset.to) {
                    toInput.value = selectedOption.dataset.to;
                }
            }

            if (monthFilter) {
                monthFilter.addEventListener('change', applySelectedMonth);
            }
        });
    </script>
@endsection

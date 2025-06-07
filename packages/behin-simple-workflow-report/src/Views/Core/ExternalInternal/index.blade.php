@extends('behin-layouts.app')

@section('title')
    گزارش پرونده‌های جاری
@endsection

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <label for="statusFilter" class="form-label fw-bold">فیلتر بر اساس وضعیت</label>
            <select id="statusFilter" multiple class="form-select select2">
                @php
                    $allStatuses = collect();
                    foreach ($cases as $case) {
                        foreach ($case->whereIs() as $inbox) {
                            $allStatuses->push($inbox->task->name);
                        }
                    }
                    $uniqueStatuses = $allStatuses->unique();
                @endphp

                @foreach ($uniqueStatuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div class="card shadow rounded-4 border-0">
            <div class="card-header bg-success text-white fs-5 fw-bold rounded-top">
                گزارش پرونده‌های جاری
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle" id="cases">
                    <thead class="table-light">
                        <tr>
                            <th>شماره پرونده</th>
                            <th>مشتری</th>
                            <th>ایجادکننده</th>
                            <th>آخرین وضعیت</th>
                            <th>تاریخ ایجاد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cases as $case)
                            <tr>
                                <td>
                                    <a href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $case->number]) }}" class="text-decoration-none me-1">
                                        <i class="fa fa-external-link text-primary"></i>
                                    </a>
                                    <span class="fw-bold">{{ $case->number }}</span>
                                    {!! $case->history !!}
                                </td>
                                <td>{{ $case->getVariable('customer_workshop_or_ceo_name') }}</td>
                                <td>{{ getUserInfo($case->creator)->name }}</td>
                                <td>
                                    @foreach ($case->whereIs() as $inbox)
                                        {!! $inbox->task->styled_name !!}
                                        <span class="text-muted small">({{ getUserInfo($inbox->actor)->name }})</span><br>
                                    @endforeach
                                </td>
                                <td dir="ltr">{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</td>
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
        $(document).ready(function() {
            initial_view()

            var table = $('#cases').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: "خروجی اکسل",
                    title: "گزارش پرونده های جاری",
                    className: "btn btn-success btn-sm",
                    exportOptions: {
                        columns: ':visible',
                        footer: true
                    }
                }],
                order: [[0, "desc"]],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                }
            });

            $('#statusFilter').on('change', function() {
                var selected = $(this).val();
                if (selected && selected.length > 0) {
                    var regex = selected.map(s => s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')).join('|');
                    table.column(3).search(regex, true, false).draw();
                } else {
                    table.column(3).search('').draw();
                }
            });
        });
    </script>
@endsection

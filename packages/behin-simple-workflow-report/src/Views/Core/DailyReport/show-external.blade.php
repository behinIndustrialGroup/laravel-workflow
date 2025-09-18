<div class="table-responsive">

    <table class="table table-striped table-hover align-middle text-center">
        <thead>
            <tr>
                <td>#</td>
                <th>شماره پرونده</th>
                <th>مشتری</th>
                <th>دستگاه</th>
                <th>تاریخ ثبت</th>
                <th>مدت</th>
                <th>گزارش</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($item->case_number)
                            <a href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $item->case_number]) }}"
                                class="text-decoration-none me-1">
                                <i class="fa fa-external-link text-primary"></i>
                            </a>
                            {{ $item->case_number ?? '' }}
                        @endif
                    </td>
                    <td>{{ $item->case() ? $item->case()->getVariable('customer_workshop_or_ceo_name') : '' }}</td>
                    <td>{{ $item->device() ? $item->device()->name : '' }}</td>
                    <td>{{ toJalali($item->created_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->duration }}</td> 
                    <td>{{ $item->report }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

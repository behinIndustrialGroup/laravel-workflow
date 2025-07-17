<div class="table-responsive">

    <table class="table">
        <thead>
            <tr>
                <td>#</td>
                <th>شماره پرونده</th>
                <th>عنوان پرونده</th>
                <th>ایجاد</th>
                <th>انجام</th>
                <th>فرایند</th>
                <th>نام فعالیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($item->case?->number)
                            <a href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $item->case?->number]) }}"
                                class="text-decoration-none me-1">
                                <i class="fa fa-external-link text-primary"></i>
                            </a>
                            {{ $item->case->number ?? '' }}
                        @endif
                    </td>
                    <td>{{ $item->case ? $item->case->getVariable('customer_workshop_or_ceo_name') : '' }}</td>
                    <td>{{ toJalali($item->created_at) }}</td>
                    <td>{{ toJalali($item->updated_at) }}</td>
                    <td>{{ $item->task->process->name ?? '' }}</td>
                    <td>{{ $item->task->name ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

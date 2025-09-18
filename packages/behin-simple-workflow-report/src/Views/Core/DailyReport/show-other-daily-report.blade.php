<div class="table-responsive">

    <table class="table table-striped table-hover align-middle text-center">
        <thead>
            <tr>
                <td>#</td>
                <th>شماره پرونده</th>
                <th>نویسنده</th>
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
                            {{ $item->case_number ?? '' }}
                        @endif
                    </td>
                    <td>{{ getUserInfo($item->created_by)->name ?? '' }}</td>
                    <td>{{ toJalali($item->created_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ round(( (int) ($item->end_alt / 1000) - (int)($item->start_alt / 1000) ) /3600 , 2) }}</td>
                    <td>{{ $item->report }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

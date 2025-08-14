<div class="table-responsive">

    <table class="table">
        <thead>
            <tr>
                <td>#</td>
                <th>شماره پرونده</th>
                <th>نویسنده</th>
                <th>شروع</th>
                <th>پایان</th>
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
                    <td>{{ toJalali( (int) ($item->start_alt / 1000) )->format('Y-m-d H:i') }}</td>
                    <td>{{ toJalali( (int) ($item->end_alt / 1000) )->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->report }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

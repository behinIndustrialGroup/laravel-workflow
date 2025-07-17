<div class="table-responsive">

    <table class="table">
        <thead>
            <tr>
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
                    <td>{{ $item->case->number ?? '' }}</td>
                    <td>{{ $item->case_name }}</td>
                    <td>{{ toJalali($item->created_at) }}</td>
                    <td>{{ toJalali($item->updated_at) }}</td>
                    <td>{{ $item->task->process->name ?? '' }}</td>
                    <td>{{ $item->task->name ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

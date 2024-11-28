<table class="table table-stripped">
    <thead>
        <tr>
            <th>نام</th>
            <th>کد مرکز</th>
            <th>شماره صنف</th>
            <th>تاریخ انقضا</th>
            <th>آدرس</th>
            <th>لوکیشن</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($agencies as $agency)
            <tr>
                <td>{{ $agency['firstname'] }}</td>
                <td>{{ $agency['agency_code'] }}</td>
                <td>{{ $agency['guild_number'] }}</td>
                <td>{{ $agency['exp_date'] }}</td>
                <td>{{ $agency['address'] }}</td>
                <td><a href="{{ route('user-profile.getLocation', [ 'parent_id' => $agency['parent_id'] ]) }}">ویرایش لوکیشن</a></td>
            </tr>
        @endforeach
    </tbody>
</table>

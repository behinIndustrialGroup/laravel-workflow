@extends('behin-layouts.app')

@section('title', 'گزارشات روزانه')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <form method="GET" action="{{ route('simpleWorkflowReport.daily-report.index') }}" class="mb-3">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="from_date">از تاریخ</label>
                            <input type="text" id="from_date" name="from_date" class="form-control persian-date" value="{{ request('from_date') }}" placeholder="مثلاً 1403/01/01">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date">تا تاریخ</label>
                            <input type="text" id="to_date" name="to_date" class="form-control persian-date" value="{{ request('to_date') }}" placeholder="مثلاً 1403/12/29">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id">پرسنل</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">همه پرسنل</option>
                                @foreach (\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">فیلتر</button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="card-body">
                <div class="col-sm-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>شماره</th>
                                <th>نام</th>
                                <th>داخلی</th>
                                <th>خارجی</th>
                                <th>مپاسنتر</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->number }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <i class="fa fa-external-link text-primary" onclick="showInternal(`{{ $user->id }}`, `{{ request('from_date') }}`, `{{ request('to_date') }}`)"></i>
                                        {{ $user->internal }}</td>
                                    <td>
                                        <i class="fa fa-external-link text-primary" onclick="showExternal(`{{ $user->id }}`, `{{ request('from_date') }}`, `{{ request('to_date') }}`)"></i>
                                        {{ $user->external }}</td>
                                    <td>{{ $user->mapa_center }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view()
        function showInternal(userId, from = '', to = ''){
            url = "{{ route('simpleWorkflowReport.daily-report.show-internal', ['user_id', 'from', 'to']) }}";
            url = url.replace('user_id', userId)
            url = url.replace('from', from)
            url = url.replace('to', to)
            open_admin_modal(url);
        }

        function showExternal(userId, from = '', to = ''){
            url = "{{ route('simpleWorkflowReport.daily-report.show-external', ['user_id', 'from', 'to']) }}";
            url = url.replace('user_id', userId)
            url = url.replace('from', from)
            url = url.replace('to', to)
            open_admin_modal(url);
        }
    </script>
@endsection

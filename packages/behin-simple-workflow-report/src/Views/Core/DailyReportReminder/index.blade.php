@extends('behin-layouts.app')

@section('title', 'گزارش پیامک یادآوری گزارش روزانه')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header rounded shadow-sm p-4 mb-4" style="background-color: #e8f6f3;">
                <form method="GET" action="{{ route('simpleWorkflowReport.daily-report.reminder-summary') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label fw-bold">از تاریخ</label>
                            <input type="text" id="from_date" name="from_date"
                                   class="form-control persian-date rounded-pill shadow-sm" value="{{ $fromDate }}"
                                   placeholder="مثلاً 1403/01/01">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label fw-bold">تا تاریخ</label>
                            <input type="text" id="to_date" name="to_date"
                                   class="form-control persian-date rounded-pill shadow-sm" value="{{ $toDate }}"
                                   placeholder="مثلاً 1403/01/31">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label fw-bold">پرسنل</label>
                            <select name="user_id" id="user_id" class="form-control select2 rounded-pill shadow-sm">
                                <option value="">همه پرسنل</option>
                                @foreach ($allUsers as $user)
                                    <option value="{{ $user->id }}" {{ (string) $selectedUserId === (string) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-grid">
                            <button type="submit" class="btn btn-success rounded-pill shadow-sm fw-bold">فیلتر</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #b8e0d2; color: #1d2d2c;">
                        <tr>
                            <th>شماره</th>
                            <th>نام</th>
                            <th>تعداد پیامک یادآوری ارسال شده</th>
                            <th>تعداد روزهای بدون گزارش پس از یادآوری</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->number }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->reminder_count ?? 0 }}</td>
                                <td>{{ $user->missing_report_count ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">رکوردی یافت نشد.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
    </script>
@endsection

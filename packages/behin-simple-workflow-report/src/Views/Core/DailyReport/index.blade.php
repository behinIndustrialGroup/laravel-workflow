@extends('behin-layouts.app')

@section('title', 'گزارشات روزانه')
@php
    use Morilog\Jalali\Jalalian;

    $todayJalali = Jalalian::now()->format('Y-m-d');
    $fromDate = request('from_date') ?? $todayJalali;
    $toDate = request('to_date') ?? $todayJalali;
@endphp


@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header rounded shadow-sm p-4 mb-4" style="background-color: #e8f6f3;">
                <form method="GET" action="{{ route('simpleWorkflowReport.daily-report.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label fw-bold">تاریخ</label>
                            <input type="text" id="from_date" name="from_date" class="form-control persian-date rounded-pill shadow-sm"
                                value="{{ $fromDate }}" placeholder="مثلاً 1403/04/01">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label fw-bold">پرسنل</label>
                            <select name="user_id" id="user_id" class="form-control select2 rounded-pill shadow-sm">
                                <option value="">همه پرسنل</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
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
                <div class="col-sm-12 table-responsive">
                    <table class="table">
                        <thead style="background-color: #b8e0d2; color: #1d2d2c;">
                            <tr>
                                <th>شماره</th>
                                <th>نام</th>
                                <th>مپاسنتر</th>
                                <th>داخلی</th>
                                <th>خارجی</th>
                                <th>همکار</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr @if ($user->internal > 0 || $user->external > 0 || $user->mapa_center > 0) style="background-color: #e6f4ea;" @endif
                                    @if($timeoffItems->where('type', 'روزانه')->where('user', $user->id)->count() > 0) style="background-color: #ffbb3d;" @endif
                                    >
                                    <td>{{ $user->number }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        @if ($user->mapa_center > 0)
                                            <i class="fa fa-external-link text-primary"
                                                onclick="showMapaCenter(`{{ $user->id }}`, `{{ $fromDate }}`, `{{ $toDate }}`)">
                                            </i>
                                            {{ $user->mapa_center }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->internal > 0)
                                            <i class="fa fa-external-link text-primary"
                                                onclick="showInternal(`{{ $user->id }}`, `{{ $fromDate }}`, `{{ $toDate }}`)">
                                            </i>
                                            {{ $user->internal }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->external > 0)
                                            <i class="fa fa-external-link text-primary"
                                                onclick="showExternal(`{{ $user->id }}`, `{{ $fromDate }}`, `{{ $toDate }}`)">
                                            </i>
                                            {{ $user->external }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->externalAsAssistant > 0)
                                            <i class="fa fa-external-link text-primary"
                                                onclick="showExternalAsAssistant(`{{ $user->id }}`, `{{ $fromDate }}`, `{{ $toDate }}`)">
                                            </i>
                                            {{ $user->externalAsAssistant }}
                                        @endif
                                    </td>
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

        function showInternal(userId, from = '', to = '') {
            url = "{{ route('simpleWorkflowReport.daily-report.show-internal', ['user_id', 'from', 'to']) }}";
            url = url.replace('user_id', userId)
            url = url.replace('from', from)
            url = url.replace('to', to)
            open_admin_modal(url);
        }

        function showExternal(userId, from = '', to = '') {
            url = "{{ route('simpleWorkflowReport.daily-report.show-external', ['user_id', 'from', 'to']) }}";
            url = url.replace('user_id', userId)
            url = url.replace('from', from)
            url = url.replace('to', to)
            open_admin_modal(url);
        }

        function showMapaCenter(userId, from = '', to = '') {
            url = "{{ route('simpleWorkflowReport.daily-report.show-mapa-center', ['user_id', 'from', 'to']) }}";
            url = url.replace('user_id', userId)
            url = url.replace('from', from)
            url = url.replace('to', to)
            open_admin_modal(url);
        }
    </script>
@endsection

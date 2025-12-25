@extends('behin-layouts.app')

@section('title', 'مدیریت امانت انبار')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">ثبت درخواست جدید</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('simpleWorkflowReport.borrow-requests.store') }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">نام کالا</label>
                            <input name="item_name" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">تعداد</label>
                            <input name="quantity" class="form-control" type="number" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">شناسه کالا (اختیاری)</label>
                            <input name="item_id" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">شماره پرونده (اختیاری)</label>
                            <input name="case_number" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">شناسه پرونده (اختیاری)</label>
                            <input name="case_id" class="form-control" type="number">
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button class="btn btn-primary" type="submit">ثبت درخواست</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">وضعیت‌ها</div>
                <ul class="list-group list-group-flush">
                    @foreach ($statuses as $statusKey => $status)
                        <li class="list-group-item">
                            <strong>{{ $status['label'] }}</strong>
                            <div class="text-muted small">{{ $status['description'] }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">درخواست‌های من</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>کالا</th>
                        <th>تعداد</th>
                        <th>وضعیت</th>
                        <th>تاریخ تحویل</th>
                        <th>تاریخ بازگشت مورد انتظار</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($myRequests as $request)
                        <tr>
                            <td>
                                <div>{{ $request->item_name }}</div>
                                @if ($request->item_id)
                                    <div class="text-muted small">شناسه: {{ $request->item_id }}</div>
                                @endif
                            </td>
                            <td>{{ $request->quantity }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $request->status_label }}</span>
                                <div class="text-muted small">{{ $statuses[$request->status]['description'] ?? '' }}</div>
                            </td>
                            <td>
                                @if ($request->delivered_at)
                                    {{ toJalali($request->delivered_at)->format('Y-m-d') }}
                                @else
                                    ـ
                                @endif
                            </td>
                            <td>
                                @if ($request->expected_return_date)
                                    {{ toJalali($request->expected_return_date)->format('Y-m-d') }}
                                @else
                                    ـ
                                @endif
                            </td>
                            <td>
                                @if ($request->status === 'delivered')
                                    <form method="POST" action="{{ route('simpleWorkflowReport.borrow-requests.mark-returned', $request) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success" type="submit">تحویل دادم</button>
                                    </form>
                                @elseif ($request->status === 'pending_confirmation')
                                    <span class="text-muted">در انتظار تایید انبار</span>
                                @elseif ($request->status === 'returned')
                                    <span class="text-success">بسته شد</span>
                                @else
                                    <span class="text-muted">در انتظار انبار</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">درخواستی ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">در انتظار تحویل از انبار</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>کالا</th>
                                <th>درخواست‌کننده</th>
                                <th>تعداد</th>
                                <th>تحویل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingDeliveries as $request)
                                <tr>
                                    <td>{{ $request->item_name }}</td>
                                    <td>{{ $request->requester_id }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('simpleWorkflowReport.borrow-requests.deliver', $request) }}" class="row g-1">
                                            @csrf
                                            <div class="col-md-6">
                                                <input type="text" name="delivered_at" class="form-control persian-date" placeholder="تاریخ تحویل" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="expected_return_date" class="form-control persian-date" placeholder="تاریخ بازگشت احتمالی">
                                            </div>
                                            <div class="col-12 mt-2">
                                                <button class="btn btn-sm btn-primary" type="submit">ثبت تحویل</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">درخواستی برای تحویل وجود ندارد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">در انتظار تایید بازگشت</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>کالا</th>
                                <th>تعداد</th>
                                <th>تاریخ اعلام بازگشت</th>
                                <th>تایید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($waitingReturnConfirmation as $request)
                                <tr>
                                    <td>{{ $request->item_name }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>
                                        @if ($request->actual_return_date)
                                            {{ toJalali($request->actual_return_date)->format('Y-m-d') }}
                                        @else
                                            ـ
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('simpleWorkflowReport.borrow-requests.confirm-return', $request) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit">تحویل گرفتم</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">درخواستی در انتظار تایید نیست.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

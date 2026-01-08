@extends('behin-layouts.app')

@section('title', 'مدیریت امانت انبار')

@php
    $myRequestsCount = $myRequests->count();
    $pendingDeliveriesCount = $pendingDeliveries->count();
    $deliveredRequestsCount = $deliveredRequests->count();
    $waitingReturnConfirmationCount = $waitingReturnConfirmation->count();
@endphp

@section('style')
    <style>
        .borrow-requests-report .filter-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(34, 197, 94, 0.08));
            border: none;
            border-radius: 16px;
        }

        .borrow-requests-report .filter-card .card-header {
            border-bottom: none;
            padding-bottom: 0;
            background: transparent;
        }

        .borrow-requests-report .filter-card .card-body {
            padding-top: 0.5rem;
        }

        .borrow-requests-report .metric-card {
            border: none;
            border-radius: 18px;
            color: #fff;
            overflow: hidden;
            position: relative;
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.18);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .borrow-requests-report .metric-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18);
        }

        .borrow-requests-report .metric-icon {
            font-size: 42px;
            opacity: 0.2;
            position: absolute;
            inset-inline-end: 16px;
            inset-block-start: 16px;
        }

        .borrow-requests-report .metric-card .metric-value {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .borrow-requests-report .metric-card .metric-label {
            font-size: 0.95rem;
            margin-top: 0.5rem;
            opacity: 0.9;
        }

        .borrow-requests-report .table-card {
            border-radius: 18px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .borrow-requests-report .table thead th {
            border: none;
            background: #f6f8fb;
            font-weight: 600;
            color: #374151;
            padding-block: 1rem;
            white-space: nowrap;
        }

        .borrow-requests-report .table tbody td {
            vertical-align: middle;
            border-top: 1px solid rgba(55, 65, 81, 0.08);
            padding-block: 0.85rem;
        }

        .borrow-requests-report .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .borrow-requests-report .empty-state .material-icons {
            font-size: 58px;
            color: #9ca3af;
            margin-bottom: 1rem;
        }

        .borrow-requests-report .form-label {
            font-weight: 600;
            color: #1f2937;
        }

        .borrow-requests-report .form-control {
            border-radius: 12px;
            border: 1px solid rgba(55, 65, 81, 0.12);
            padding: 0.6rem 0.85rem;
            box-shadow: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .borrow-requests-report .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
        }

        .borrow-requests-report .btn-primary {
            border-radius: 12px;
            padding-block: 0.6rem;
            font-weight: 600;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
        }

        .borrow-requests-report .btn-outline-primary {
            border-radius: 12px;
        }

        .borrow-requests-report .btn-outline-secondary {
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .borrow-requests-report .metric-card {
                margin-bottom: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="borrow-requests-report">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card filter-card shadow-sm">
                    <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                        <div>
                            <h5 class="mb-1 fw-bold">مرور وضعیت امانت انبار</h5>
                            <p class="text-muted mb-0">نمای کلی درخواست‌ها و وضعیت تحویل و بازگشت کالاها</p>
                        </div>
                        <span class="badge bg-success-subtle text-success mt-2 mt-lg-0">به‌روزرسانی لحظه‌ای</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card metric-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <div class="card-body">
                        <span class="material-icons metric-icon">inventory_2</span>
                        <div class="metric-value">{{ number_format($myRequestsCount) }}</div>
                        <div class="metric-label">کل درخواست‌های من</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card metric-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                    <div class="card-body">
                        <span class="material-icons metric-icon">hourglass_top</span>
                        <div class="metric-value">{{ number_format($pendingDeliveriesCount) }}</div>
                        <div class="metric-label">در انتظار تحویل انبار</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card metric-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <div class="card-body">
                        <span class="material-icons metric-icon">local_shipping</span>
                        <div class="metric-value">{{ number_format($deliveredRequestsCount) }}</div>
                        <div class="metric-label">اقلام تحویل داده شده</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card metric-card" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <div class="card-body">
                        <span class="material-icons metric-icon">assignment_turned_in</span>
                        <div class="metric-value">{{ number_format($waitingReturnConfirmationCount) }}</div>
                        <div class="metric-label">در انتظار تایید بازگشت</div>
                    </div>
                </div>
            </div>
        </div>

        @if (!access('انبار گردانی کالاهای امانی'))

            <div class="row g-3 mb-3">
                <div class="col-lg-8">
                    <div class="card filter-card shadow-sm">
                        <div class="card-header">ثبت درخواست جدید</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('simpleWorkflowReport.borrow-requests.store') }}"
                                class="row g-2">
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
                <div class="col-lg-4">
                    <div class="card table-card h-100">
                        <div class="card-header bg-white border-0 fw-bold">وضعیت‌ها</div>
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

            <div class="card table-card mb-4">
                <div class="card-header bg-white border-0 fw-bold">درخواست‌های من</div>
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
                                        <div class="text-muted small">
                                            {{ $statuses[$request->status]['description'] ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($request->delivered_at_alt)
                                            {{ toJalali((int) $request->delivered_at_alt)->format('Y-m-d') }}
                                        @else
                                            ـ
                                        @endif
                                    </td>
                                    <td>
                                        @if ($request->expected_return_date_alt)
                                            {{ toJalali((int) $request->expected_return_date_alt)->format('Y-m-d') }}
                                        @else
                                            ـ
                                        @endif
                                    </td>
                                    <td>
                                        @if ($request->status === 'delivered')
                                            <form method="POST"
                                                action="{{ route('simpleWorkflowReport.borrow-requests.mark-returned', $request) }}">
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
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <span class="material-icons">inventory</span>
                                            <div>درخواستی ثبت نشده است.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif


        @if (access('انبار گردانی کالاهای امانی'))
            <div class="row g-3">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0 fw-bold">در انتظار تحویل از انبار</div>
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
                                                <form method="POST"
                                                    action="{{ route('simpleWorkflowReport.borrow-requests.deliver', $request) }}"
                                                    class="row g-1">
                                                    @csrf
                                                    <div class="col-md-6">
                                                        <input type="text" name="delivered_at"
                                                            class="form-control persian-datetime"
                                                            placeholder="تاریخ تحویل" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="expected_return_date"
                                                            class="form-control persian-datetime"
                                                            placeholder="تاریخ بازگشت احتمالی">
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <button class="btn btn-sm btn-primary" type="submit">ثبت تحویل</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-state">
                                                    <span class="material-icons">warehouse</span>
                                                    <div>درخواستی برای تحویل وجود ندارد.</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0 fw-bold">درخواست‌های تحویل داده شده (در حال امانت)</div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>کالا</th>
                                        <th>تحویل</th>
                                        <th>بازگشت احتمالی</th>
                                        <th>به‌روزرسانی</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deliveredRequests as $request)
                                        <tr>
                                            <td>
                                                <div>{{ $request->item_name }}</div>
                                                <div class="text-muted small">درخواست‌کننده:
                                                    {{ getUserInfo($request->requester_id)?->name ?? '' }}</div>
                                            </td>
                                            <td>{{ $request->delivered_at ?? '' }}</td>
                                            <td>{{ $request->expected_return_date ?? '' }}</td>
                                            <td>
                                                <form method="POST"
                                                    action="{{ route('simpleWorkflowReport.borrow-requests.deliver', $request) }}"
                                                    class="row g-1">
                                                    @csrf
                                                    <div class="col-12">
                                                        <input type="text" name="delivered_at"
                                                            class="form-control persian-datetime"
                                                            placeholder="تاریخ تحویل"
                                                            value="{{ $request->delivered_at ?? ($request->delivered_at ? toJalali($request->delivered_at)->format('Y-m-d') : '') }}"
                                                            required>
                                                    </div>
                                                    <div class="col-12 mt-1">
                                                        <input type="text" name="expected_return_date"
                                                            class="form-control persian-datetime"
                                                            placeholder="تاریخ بازگشت احتمالی"
                                                            value="{{ $request->expected_return_date ?? ($request->expected_return_date ? toJalali($request->expected_return_date)->format('Y-m-d') : '') }}">
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <button class="btn btn-sm btn-outline-primary w-100"
                                                            type="submit">ویرایش تاریخ‌ها</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-state">
                                                    <span class="material-icons">local_shipping</span>
                                                    <div>درخواست تحویل داده‌شده‌ای برای ویرایش وجود ندارد.</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card mb-4">
                        <div class="card-header bg-white border-0 fw-bold">در انتظار تایید بازگشت</div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>کالا</th>
                                        <th>تعداد</th>
                                        <th>درخواست کننده</th>
                                        <th>تاریخ اعلام بازگشت</th>
                                        <th>تایید</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($waitingReturnConfirmation as $request)
                                        <tr>
                                            <td>{{ $request->item_name }}</td>
                                            <td>{{ $request->quantity }}</td>
                                            <td>{{ getUserInfo($request->requester_id)->name ?? '' }}</td>
                                            <td>
                                                @if ($request->actual_return_date)
                                                    {{ toJalali($request->actual_return_date)->format('Y-m-d') }}
                                                @else
                                                    ـ
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST"
                                                    action="{{ route('simpleWorkflowReport.borrow-requests.confirm-return', $request) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success" type="submit">تحویل گرفتم</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <span class="material-icons">assignment_turned_in</span>
                                                    <div>درخواستی در انتظار تایید نیست.</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@extends('behin-layouts.app')

@section('title', 'گزارش پیامک یادآوری گزارش روزانه')

@php
    $rewardDetailsByUser = $rewardDetailsByUser ?? [];
    $penaltyDetailsByUser = $penaltyDetailsByUser ?? [];
@endphp

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
                            <th>پاداش‌های متفرقه</th>
                            <th>جرایم متفرقه</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->number }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->reminder_count ?? 0 }}</td>
                                <td>{{ $user->missing_report_count ?? 0 }}</td>
                                @php
                                    $rewardCount = $user->reward_misc_count ?? 0;
                                    $penaltyCount = $user->penalty_misc_count ?? 0;
                                    $rewardItems = $rewardDetailsByUser[$user->id] ?? [];
                                    $penaltyItems = $penaltyDetailsByUser[$user->id] ?? [];
                                @endphp
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success">{{ $rewardCount }}</span>
                                        <button type="button"
                                                class="btn btn-outline-success btn-sm js-reward-penalty-modal"
                                                data-type="پاداش‌های متفرقه برای {{ $user->name }}"
                                                data-items='@json($rewardItems)'
                                                title="نمایش جزئیات پاداش‌ها"
                                                {{ $rewardCount === 0 ? 'disabled' : '' }}>
                                            <i class="material-icons" style="font-size: 16px;">visibility</i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-danger">{{ $penaltyCount }}</span>
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm js-reward-penalty-modal"
                                                data-type="جرایم متفرقه برای {{ $user->name }}"
                                                data-items='@json($penaltyItems)'
                                                title="نمایش جزئیات جرایم"
                                                {{ $penaltyCount === 0 ? 'disabled' : '' }}>
                                            <i class="material-icons" style="font-size: 16px;">visibility</i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">رکوردی یافت نشد.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rewardPenaltyModal" tabindex="-1" aria-labelledby="rewardPenaltyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rewardPenaltyModalLabel">جزئیات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>توضیحات</th>
                                <th class="text-end">مبلغ (ریال)</th>
                                <th class="text-center">تاریخ ثبت</th>
                            </tr>
                            </thead>
                            <tbody id="rewardPenaltyModalTableBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted">رکوردی ثبت نشده است.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();

        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('rewardPenaltyModal');
            if (!modalElement || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                return;
            }

            const modalInstance = new bootstrap.Modal(modalElement);
            const modalTitle = document.getElementById('rewardPenaltyModalLabel');
            const modalBody = document.getElementById('rewardPenaltyModalTableBody');

            document.querySelectorAll('.js-reward-penalty-modal').forEach(function (button) {
                button.addEventListener('click', function () {
                    let items = [];
                    try {
                        items = JSON.parse(button.getAttribute('data-items') || '[]');
                    } catch (error) {
                        items = [];
                    }

                    modalTitle.textContent = button.getAttribute('data-type') || 'جزئیات';
                    modalBody.innerHTML = '';

                    if (!items.length) {
                        const emptyRow = document.createElement('tr');
                        const emptyCell = document.createElement('td');
                        emptyCell.colSpan = 3;
                        emptyCell.classList.add('text-center', 'text-muted');
                        emptyCell.textContent = 'رکوردی ثبت نشده است.';
                        emptyRow.appendChild(emptyCell);
                        modalBody.appendChild(emptyRow);
                    } else {
                        items.forEach(function (item) {
                            const row = document.createElement('tr');

                            const descriptionCell = document.createElement('td');
                            descriptionCell.textContent = item.description || '-';
                            row.appendChild(descriptionCell);

                            const amountCell = document.createElement('td');
                            amountCell.classList.add('text-end');
                            amountCell.textContent = item.formatted_amount || item.amount || '0';
                            row.appendChild(amountCell);

                            const dateCell = document.createElement('td');
                            dateCell.classList.add('text-center');
                            dateCell.textContent = item.recorded_at || '-';
                            row.appendChild(dateCell);

                            modalBody.appendChild(row);
                        });
                    }

                    modalInstance.show();
                });
            });
        });
    </script>
@endsection

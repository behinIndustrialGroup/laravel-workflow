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
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-success"
                                              data-reward-count-for="{{ $user->id }}">{{ $rewardCount }}</span>
                                        <button type="button"
                                                class="btn btn-outline-success btn-sm js-reward-penalty-modal"
                                                data-detail-type="reward"
                                                data-user-id="{{ $user->id }}"
                                                data-type="پاداش‌های متفرقه برای {{ $user->name }}"
                                                data-items='@json($rewardItems)'
                                                title="نمایش جزئیات پاداش‌ها"
                                                {{ $rewardCount === 0 ? 'disabled' : '' }}>
                                            <i class="material-icons" style="font-size: 16px;">visibility</i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm js-open-reward-penalty-form"
                                                data-type="reward"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                title="افزودن پاداش">
                                            <i class="material-icons" style="font-size: 16px;">add_circle</i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-danger"
                                              data-penalty-count-for="{{ $user->id }}">{{ $penaltyCount }}</span>
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm js-reward-penalty-modal"
                                                data-detail-type="penalty"
                                                data-user-id="{{ $user->id }}"
                                                data-type="جرایم متفرقه برای {{ $user->name }}"
                                                data-items='@json($penaltyItems)'
                                                title="نمایش جزئیات جرایم"
                                                {{ $penaltyCount === 0 ? 'disabled' : '' }}>
                                            <i class="material-icons" style="font-size: 16px;">visibility</i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm js-open-reward-penalty-form"
                                                data-type="penalty"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                title="افزودن جریمه">
                                            <i class="material-icons" style="font-size: 16px;">add_circle</i>
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

    <div class="modal fade" id="rewardPenaltyFormModal" tabindex="-1" aria-labelledby="rewardPenaltyFormModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rewardPenaltyFormModalLabel">ثبت پاداش/جریمه</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <form id="rewardPenaltyForm">
                    <div class="modal-body">
                        <div id="rewardPenaltyFormSuccess" class="alert alert-success d-none"></div>
                        <div id="rewardPenaltyFormErrors" class="alert alert-danger d-none"></div>

                        <input type="hidden" id="rewardPenaltyFormUserId" name="user_id">
                        <input type="hidden" id="rewardPenaltyFormType" name="type">

                        <div class="mb-3">
                            <label class="form-label fw-bold">پرسنل</label>
                            <input type="text" class="form-control" id="rewardPenaltyFormUserName" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">نوع</label>
                            <input type="text" class="form-control" id="rewardPenaltyFormTypeLabel" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="rewardPenaltyFormDescription" class="form-label fw-bold">توضیحات</label>
                            <textarea class="form-control" id="rewardPenaltyFormDescription" name="description" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="rewardPenaltyFormAmount" class="form-label fw-bold">مبلغ (ریال)</label>
                            <input type="number" class="form-control" id="rewardPenaltyFormAmount" name="amount" min="0" step="0.01" required>
                        </div>

                        <div class="mb-0">
                            <label for="rewardPenaltyFormRecordedAt" class="form-label fw-bold">تاریخ ثبت (اختیاری)</label>
                            <input type="text" class="form-control persian-date" id="rewardPenaltyFormRecordedAt" name="recorded_at" placeholder="مثلاً 1403-01-01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                        <button type="submit" class="btn btn-primary">ثبت</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();

        const rewardDetailsState = @json((object) $rewardDetailsByUser);
        const penaltyDetailsState = @json((object) $penaltyDetailsByUser);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const storeRewardPenaltyUrl = '{{ route('simpleWorkflowReport.rewards-penalties.store') }}';

        function ensureArray(state, key) {
            if (!Array.isArray(state[key])) {
                state[key] = [];
            }

            return state[key];
        }

        function updateRewardPenaltyDisplay(userId, type) {
            const state = type === 'reward' ? rewardDetailsState : penaltyDetailsState;
            const details = ensureArray(state, userId);
            const count = details.length;

            const countSelector = type === 'reward' ? `[data-reward-count-for="${userId}"]` : `[data-penalty-count-for="${userId}"]`;
            const countBadge = document.querySelector(countSelector);
            if (countBadge) {
                countBadge.textContent = count;
            }

            const detailButton = document.querySelector(`.js-reward-penalty-modal[data-detail-type="${type}"][data-user-id="${userId}"]`);
            if (detailButton) {
                detailButton.dataset.items = JSON.stringify(details);
                if (count === 0) {
                    detailButton.setAttribute('disabled', 'disabled');
                } else {
                    detailButton.removeAttribute('disabled');
                }
            }
        }

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

            const formModalElement = document.getElementById('rewardPenaltyFormModal');
            const formElement = document.getElementById('rewardPenaltyForm');
            const formSuccessAlert = document.getElementById('rewardPenaltyFormSuccess');
            const formErrorsAlert = document.getElementById('rewardPenaltyFormErrors');
            const descriptionInput = document.getElementById('rewardPenaltyFormDescription');
            const amountInput = document.getElementById('rewardPenaltyFormAmount');
            const recordedAtInput = document.getElementById('rewardPenaltyFormRecordedAt');
            const userIdInput = document.getElementById('rewardPenaltyFormUserId');
            const userNameInput = document.getElementById('rewardPenaltyFormUserName');
            const typeInput = document.getElementById('rewardPenaltyFormType');
            const typeLabelInput = document.getElementById('rewardPenaltyFormTypeLabel');

            const formModalInstance = formModalElement ? new bootstrap.Modal(formModalElement) : null;

            function resetFormAlerts() {
                if (formSuccessAlert) {
                    formSuccessAlert.classList.add('d-none');
                    formSuccessAlert.textContent = '';
                }
                if (formErrorsAlert) {
                    formErrorsAlert.classList.add('d-none');
                    formErrorsAlert.innerHTML = '';
                }
            }

            document.querySelectorAll('.js-open-reward-penalty-form').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (!formModalInstance) {
                        return;
                    }

                    resetFormAlerts();

                    descriptionInput.value = '';
                    amountInput.value = '';
                    recordedAtInput.value = '';

                    const userId = button.getAttribute('data-user-id') || '';
                    const userName = button.getAttribute('data-user-name') || '';
                    const type = button.getAttribute('data-type') || 'reward';

                    userIdInput.value = userId;
                    userNameInput.value = userName;
                    typeInput.value = type;
                    typeLabelInput.value = type === 'reward' ? 'پاداش متفرقه' : 'جریمه متفرقه';

                    const modalTitle = document.getElementById('rewardPenaltyFormModalLabel');
                    if (modalTitle) {
                        modalTitle.textContent = type === 'reward' ? 'افزودن پاداش متفرقه' : 'افزودن جریمه متفرقه';
                    }

                    formModalInstance.show();
                });
            });

            if (formElement) {
                formElement.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    resetFormAlerts();

                    const submitButton = formElement.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    const payload = {
                        user_id: userIdInput.value,
                        type: typeInput.value,
                        description: descriptionInput.value,
                        amount: amountInput.value,
                        recorded_at: recordedAtInput.value,
                    };

                    try {
                        const response = await fetch(storeRewardPenaltyUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors || {};
                            const messages = Object.values(errors).flat();

                            if (messages.length && formErrorsAlert) {
                                formErrorsAlert.innerHTML = messages.map(message => `<div>${message}</div>`).join('');
                                formErrorsAlert.classList.remove('d-none');
                            } else if (formErrorsAlert) {
                                formErrorsAlert.textContent = data?.message || 'خطا در ثبت رکورد رخ داده است.';
                                formErrorsAlert.classList.remove('d-none');
                            }

                            return;
                        }

                        const record = data?.data;
                        if (record) {
                            const userKey = String(record.user_id);
                            const state = record.type === 'reward' ? rewardDetailsState : penaltyDetailsState;
                            const details = ensureArray(state, userKey);

                            details.push({
                                description: record.description,
                                amount: record.amount,
                                formatted_amount: record.formatted_amount,
                                recorded_at: record.recorded_at,
                            });

                            updateRewardPenaltyDisplay(userKey, record.type);

                            descriptionInput.value = '';
                            amountInput.value = '';
                            recordedAtInput.value = '';

                            if (formSuccessAlert) {
                                formSuccessAlert.textContent = data?.message || 'رکورد با موفقیت ثبت شد.';
                                formSuccessAlert.classList.remove('d-none');
                            }
                        }
                    } catch (error) {
                        if (formErrorsAlert) {
                            formErrorsAlert.textContent = 'امکان برقراری ارتباط با سرور وجود ندارد.';
                            formErrorsAlert.classList.remove('d-none');
                        }
                    } finally {
                        if (submitButton) {
                            submitButton.disabled = false;
                        }
                    }
                });
            }
        });
    </script>
@endsection

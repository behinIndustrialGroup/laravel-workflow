<div>
    @if ($payments->count())
        <hr>
        <h6 class="mb-3 text-center text-secondary">پرداخت‌های ثبت شده</h6>
        <ul class="list-group shadow-sm">
            @foreach ($payments as $payment)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    @if ($payment->payment_type == 'نقدی')
                        <span class="fw-bold text-primary">{{ $payment->payment_type }}</span>
                        <span class="fw-bold text-primary">{{ toJalali((int) $payment->date)->format('Y/m/d') }}</span>
                        <span class="fw-bold text-primary">{{ $payment->account_number }}</span>
                        <span class="fw-bold text-primary">{{ $payment->account_name }}</span>
                        <span class="badge bg-success rounded-pill">{{ number_format($payment->amount) }} ریال</span>
                    @endif
                    @if ($payment->payment_type == 'چک')
                        <span class="fw-bold text-primary">{{ $payment->payment_type }}</span>
                        <span
                            class="fw-bold text-primary">{{ toJalali((int) $payment->cheque_due_date)->format('Y/m/d') }}</span>
                        <span class="fw-bold text-primary">{{ $payment->cheque_number }}</span>
                        <span class="fw-bold text-primary">{{ $payment->bank_name }}</span>
                        <span class="badge bg-success rounded-pill">{{ number_format($payment->amount) }} ریال</span>
                    @endif
                    <form action="{{ route('simpleWorkflowReport.on-credit-report.destroy', $payment->id) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>

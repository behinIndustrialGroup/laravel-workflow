<div>
    @if($payments->count())
        <hr>
        <h6 class="mb-3 text-center text-secondary">پرداخت‌های ثبت شده</h6>
        <ul class="list-group shadow-sm">
            @foreach($payments as $payment)
            @if($payment->payment_type == 'نقدی')
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary">{{ $payment->payment_type }}</span>
                    <span class="fw-bold text-primary">{{ toJalali((int)$payment->date)->format('Y/m/d') }}</span>
                    <span class="fw-bold text-primary">{{ $payment->account_number }}</span>
                    <span class="fw-bold text-primary">{{ $payment->account_name }}</span>
                    <span class="badge bg-success rounded-pill">{{ number_format($payment->amount) }} ریال</span>
                </li>
            @endif
            @if($payment->payment_type == 'چک')
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary">{{ $payment->payment_type }}</span>
                    <span class="fw-bold text-primary">{{ toJalali((int)$payment->date)->format('Y/m/d') }}</span>
                    <span class="fw-bold text-primary">{{ $payment->cheque_number }}</span>
                    <span class="fw-bold text-primary">{{ $payment->bank_name }}</span>
                    <span class="badge bg-success rounded-pill">{{ number_format($payment->amount) }} ریال</span>
                </li>
            @endif
            @endforeach
        </ul>
    @endif
</div>

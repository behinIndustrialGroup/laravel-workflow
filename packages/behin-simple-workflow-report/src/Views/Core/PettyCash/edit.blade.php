@extends('behin-layouts.app')

@section('title', 'ویرایش تنخواه')
@php
    use Morilog\Jalali\Jalalian;
@endphp

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('simpleWorkflowReport.petty-cash.update', $pettyCash) }}" class="row g-2">
                @csrf
                @method('PUT')
                <div class="col-md-3">
                    <label for="title">عنوان خرج</label>
                    <input name="title" class="form-control" value="{{ $pettyCash->title }}" required>
                </div>
                <div class="col-md-2">
                    <label for="amount">مبلغ</label>
                    <input name="amount" class="form-control formatted-digit" value="{{ $pettyCash->amount }}" required>
                </div>
                <div class="col-md-3">
                    <label for="paid_at">تاریخ پرداخت</label>
                    <input type="text" name="paid_at" class="form-control persian-date" value="{{ toJalali($pettyCash->paid_at)->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label for="from_account">از حساب</label>
                    <input name="from_account" class="form-control" value="{{ $pettyCash->from_account }}">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary" type="submit">ذخیره</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
    </script>
@endsection

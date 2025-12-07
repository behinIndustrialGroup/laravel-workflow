@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">ویرایش طرف حساب</div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <form action="{{ route('simpleWorkflowReport.counter-party.update', $counterParty->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">نام</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $counterParty->name) }}" autofocus>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="account_number">شماره حساب</label>
                                <input type="text" name="account_number" id="account_number" class="form-control"
                                    value="{{ old('account_number', $counterParty->account_number) }}">
                                @error('account_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_id">کاربر مرتبط</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">-</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $counterParty->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $counterParty->description) }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">ذخیره</button>
                    <a href="{{ route('simpleWorkflowReport.counter-party.index') }}" class="btn btn-secondary">بازگشت</a>
                </form>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">مرج طرف حساب</div>
            <div class="card-body">
                <form action="{{ route('simpleWorkflowReport.counter-party.merge') }}" method="POST">
                    @csrf
                    <input type="hidden" name="from_counterparty_id" value="{{ $counterParty->id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>طرف حساب اول</label>
                                <input type="text" class="form-control" value="{{ $counterParty->name }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_counterparty_id">طرف حساب دوم</label>
                                <select name="to_counterparty_id" id="to_counterparty_id" class="form-control select2">
                                    <option value="">انتخاب کنید</option>
                                    @foreach ($counterParties as $item)
                                        <option value="{{ $item->id }}" @selected(old('to_counterparty_id') == $item->id)>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_counterparty_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        با مرج کردن، تمامی تراکنش‌ها و فاکتورهای مالی مرتبط با طرف حساب اول به طرف حساب دوم منتقل می‌شود.
                    </div>
                    <button type="submit" class="btn btn-danger">مرج طرف حساب</button>
                </form>
            </div>
        </div>
    </div>
@endsection

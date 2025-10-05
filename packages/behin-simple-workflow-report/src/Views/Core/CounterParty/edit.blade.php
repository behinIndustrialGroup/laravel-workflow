@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">ویرایش طرف حساب</div>
            <div class="card-body">
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
    </div>
@endsection

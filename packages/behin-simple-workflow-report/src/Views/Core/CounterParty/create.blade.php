@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">ایجاد پرونده</div>
            <div class="card-body">
                <form action="{{ route('simpleWorkflowReport.counter-party.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">نام</label>
                                <input type="text" name="name" id="name" class="form-control" autofocus focus value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="account_number">شماره حساب</label>
                                <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}">
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
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
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
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">ایجاد</button>
                </form>
            </div>
        </div>
    </div>
@endsection

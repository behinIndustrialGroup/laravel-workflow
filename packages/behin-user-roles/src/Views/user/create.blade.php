@extends('behin-layouts.app')

@section('title')
    ایجاد کاربر
@endsection

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card p-2">
            <a href="{{ route('user.all', 'all') }}" class="btn btn-primary">بازگشت به لیست کاربران</a>
            <div class="row mb-3">
                <form action="{{ route('users.store') }}" method="POST" class="row col-12 form-horizontal">
                    @csrf
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">نام</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="email">نام کاربری</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="role">نقش</label>
                            <select class="custom-select" name="role_id" id="role_id">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        @if (old('role_id') == $role->id) {{ 'selected' }} @endif>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="password">رمز عبور</label>
                            <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary">{{ __('fields.Save') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

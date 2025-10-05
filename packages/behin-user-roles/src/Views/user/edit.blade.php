@extends('behin-layouts.app')

@section('title')
    کاربران
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
                
                <form action="{{ route('user.update', $user->id) }}" method="POST" class="row col-12 form-horizontal">
                    @csrf
                    @method('PUT')
                    <div class="col-6">
                        <div class="form-group">
                            <label for="id">شناسه</label>
                            <input type="text" class="form-control" id="id" value="{{ $user->id }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="id">شماره پرسنلی</label>
                            <input type="text" name="number" class="form-control" id="id" value="{{ $user->number }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">نام</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="email">نام کاربری</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="email">ورود با آیپی</label>
                            <select name="login_with_ip" id="" class="form-control col-sm-3 mb-3">
                                <option value="1" {{ $user->login_with_ip ? 'selected' : '' }}>فعال</option>
                                <option value="0" {{ $user->login_with_ip ? '' : 'selected' }}>غیرفعال</option>
                            </select>
                            <input type="text" class="form-control" id="valid_ip" name="valid_ip" value="{{ $user->valid_ip }}" placeholder="1.1.1.1" dir="ltr">
                        </div>
                    </div>
                    <div class="col-6">
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary">{{ __('fields.Update') }}</button>
                    </div>
                </form>
            </div>
            <form action="{{ route('user.ChangePass', ['id' => $user->id]) }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{{ __('fields.New Password') }}</span>
                    </div>
                    <input type="password" class="form-control" name="pass" placeholder="{{ __('fields.New Password') }}"
                        aria-label="Password" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit">{{ __('fields.Change') }}</button>
                    </div>
                </div>
            </form>
            <hr>
            <form action="{{ route('role.changeUserRole') }}" method="POST">

                <div class="input-group mb-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="role_id">{{ __('fields.Role') }}</label>
                    </div>
                    <select class="custom-select" name="role_id" id="role_id">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                @if ($user->role_id == $role->id) {{ 'selected' }} @endif>
                                {{ $role->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit">{{ __('fields.Change') }}</button>
                    </div>
                </div>
            </form>

        </div>
        <div class="card p-2">
            @foreach ($user->departments() as $item)
                <div class="card col-sm-2">
                    <div class="card-header">
                        {{ $item->department()->name }}
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('user.removeFromDepartment', ['id' => $user->id]) }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="departmentId" value="{{ $item->department()->id }}">
                            <button type="submit" class="btn btn-danger">{{ __('fields.Remove') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
            <form method="post" action="{{ route('user.addToDepartment', ['id' => $user->id]) }}">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="department_id">{{ __('fields.Department') }}</label>
                    </div>
                    <select class="custom-select" name="department_id" id="department_id">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit">{{ __('fields.Add') }}</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card p-2">
            @php
                $selectedCounterParties = old('counterparty_ids', $user->counterParties->pluck('id')->toArray());
            @endphp
            <form method="post" action="{{ route('user.updateCounterParties', ['id' => $user->id]) }}">
                @csrf
                <div class="form-group">
                    <label for="counterparty_ids">طرف حساب های مرتبط</label>
                    <select class="form-control select2" name="counterparty_ids[]" id="counterparty_ids" multiple size="8">
                        @foreach ($counterParties as $counterParty)
                            @php
                                $isSelected = in_array($counterParty->id, $selectedCounterParties, true);
                                $assignedToOtherUser = $counterParty->user && $counterParty->user->id !== $user->id;
                            @endphp
                            <option value="{{ $counterParty->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $counterParty->name }} - {{ $counterParty->account_number ?? 'بدون شماره حساب' }}
                                @if ($assignedToOtherUser)
                                    (اختصاص یافته به {{ $counterParty->user->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('counterparty_ids')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                    @error('counterparty_ids.*')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">برای انتخاب چند گزینه از کلیدهای Ctrl یا Shift استفاده کنید.</small>
                </div>
                <button class="btn btn-outline-primary" type="submit">ثبت طرف حساب ها</button>
            </form>
            <div class="mt-3">
                <h5>طرف حساب های کاربر</h5>
                <ul class="list-group">
                    @forelse ($user->counterParties as $counterParty)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $counterParty->name }}</span>
                            <span class="badge badge-primary badge-pill" dir="ltr">{{ $counterParty->account_number ?? '-' }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">طرف حسابی برای این کاربر تعریف نشده است.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="card p-2">
            <div class="card-body row">
                <form action="{{ route('user.disable', $user->id) }}" class="col-sm-4" method="POST" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید کاربر را از همه دستگاه‌ها خارج کنید؟');">
                    @csrf
                    <button type="submit" class="btn {{ $user->is_disabled ? 'bg-success' : 'bg-danger' }}" >{{ $user->is_disabled ? "فعال کردن کاربر" : "غیرفعال کردن کاربر" }}</button>
                </form>
                <form action="{{ route('user.invalidateSessions', $user->id) }}" class="col-sm-4" method="POST" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید کاربر را از همه دستگاه‌ها خارج کنید؟');">
                    @csrf
                    <button type="submit" class="btn btn-danger">خروج از همه دستگاه‌ها</button>
                </form>
                <form action="{{ route('user.destroy', $user->id) }}" class="col-sm-4" method="POST" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید کاربر را حذف کنید؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="float: left">حذف کاربر</button>
                </form>
            </div>
        </div>
    </div>
    
    
@endsection

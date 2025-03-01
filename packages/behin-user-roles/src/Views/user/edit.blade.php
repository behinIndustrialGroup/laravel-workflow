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
    </div>
    {{-- <div class="row">
        <div class="col-sm-12">
            <a href="all" class="btn btn-info">Back To List</a>
        </div>
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">

                </div>

                <div class="box-body">
                    <table class="table">
                        <tr>
                            <th>شناسه</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>نام</th>
                            <td>{{ $user->display_name }}</td>
                        </tr>
                        <tr>
                            <th>نام کاربری</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>ایمیل</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <form method="post" action="{{ route('change-user-ip', ['id' => $user->id]) }}">
                                @csrf
                                <td>valid ip:<input type="text" name="valid_ip" id=""
                                        value="{{ $user->valid_ip }}"></td>
                                <td><input type="submit" value="ثبت ip" name="" id=""></td>
                            </form>
                        </tr>
                        <tr>
                            <form method="post" action="{{ route('change-pm-username', ['id' => $user->id]) }}">
                                @csrf
                                <td>نام کاربری Process Maker<input type="text" name="pm_username" id=""
                                        value="{{ $user->pm_username }}"></td>
                                <td><input type="submit" value="تغییر نام کاربری PM" name="" id=""></td>
                            </form>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button class="btn btn-danger"
                                    onclick="create_pm_user()">{{ __('Create PM User') }}</button>
                            </td>
                        </tr>
                        <tr>

                        </tr>
                    </table>
                </div>
            </div>

            <div class="box">
                <div class="box-body">
                    <form method="post" action="{{ $user->id }}/changeShowInReport">
                        @csrf
                        <input type="checkbox" name="showInReport" class="" <?php if ($user->showInReport == 1) {
                            echo 'checked';
                        } ?>>نمایش در گزارشها
                        <input type="submit" class="btn btn-success" value="ثبت">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h3>دسترسی ها</h3>
                </div>

                <div class="box-body">
                    <button class="" id="check_all">انتخاب همه</button>
                    <form class="form-horizontal" method="post" action="" id="role-table">
                        @csrf
                        <input type="text" name="user_id" id="" value="{{ $user->id }}">
                        <select name="role_id" id="">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    @if ($user->role_id == $role->id) {{ 'selected' }} @endif>{{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <button onclick="change_role()">change role</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

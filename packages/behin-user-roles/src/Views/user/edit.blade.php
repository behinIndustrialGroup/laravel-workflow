@extends('behin-layouts.app')

@section('title')
    کاربران
@endsection

@section('content')
    <div class="container">
        <div class="card p-2">
            <a href="{{ route('user.all', 'all') }}" class="btn btn-primary">بازگشت به لیست کاربران</a>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="id">شناسه</label>
                        <input type="text" class="form-control" id="id" value="{{ $user->id }}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="name">نام</label>
                        <input type="text" class="form-control" id="name" value="{{ $user->name }}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="email">نام کاربری</label>
                        <input type="text" class="form-control" id="email" value="{{ $user->email }}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="role">نقش</label>
                        <input type="text" class="form-control" id="role" value="{{ $user->role()->name ?? '' }}" readonly>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">{{ __('fields.New Password') }}</span>
                </div>
                <input type="password" class="form-control" name="pass" placeholder="{{ __('fields.New Password') }}" aria-label="Password" aria-describedby="basic-addon1">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit">{{ __('fields.Change') }}</button>
                </div>
            </div>
            <hr>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="role_id">{{ __('fields.Role') }}</label>
                </div>
                <select class="custom-select" name="role_id" id="role_id">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @if ($user->role_id == $role->id) {{ 'selected' }} @endif>
                            {{ $role->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit">{{ __('fields.Change') }}</button>
                </div>
            </div>
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
@section('script')
    <script>
        function change_role() {
            send_ajax_request(
                "{{ route('role.changeUserRole') }}",
                $('#role-table').serialize(),
                function(response) {
                    console.log(response);
                }
            )
        }
        $("#check_all").on('click', function() {
            $('#access_tbl input:checkbox').prop('checked', 'true');
        });

        // function create_pm_user(){
        //     var fd = new FormData();
        //     fd.append('username', "{{ $user->pm_username }}");
        //     fd.append('firstname', "{{ $user->name }}");
        //     fd.append('lastname', "{{ $user->name }}");
        //     send_ajax_formdata_request(
        //         '{{ route('MkhodrooProcessMaker.api.user.create') }}',
        //         fd,
        //         function(response){
        //             console.log(response);

        //         }
        //     )
        // }
    </script>
@endsection

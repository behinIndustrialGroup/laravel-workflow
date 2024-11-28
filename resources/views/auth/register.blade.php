@extends('behin-layouts.welcome')

@section('content')
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center col-sm-12">
                <img src="{{ url('public/behin/logo.png') . '?' . config('app.version')}}" class="col-sm-12" alt="">
            </div>
            <div class="card-body">
                <form action="javascript:void(0)" method="post" id="register-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="name" placeholder="نام و نام خانوادگی">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="email" placeholder="موبایل">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="رمز عبور">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary col-sm-12" onclick="submit()">ثبت نام</button>
                </div>
                <hr>
                <div style="text-align: center">
                    <a href="{{ route('login') }}" class="text-center">صفحه ورود</a>
                </div>
                <hr>
                <div class="center-align" style="text-align: center">
                    <a href="{{ route('password.request') }}" class="text-center">فراموشی رمز</a>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        function submit() {
            send_ajax_request(
                "{{ route('register') }}",
                $('#register-form').serialize(),
                function(response) {
                    show_message("به صفحه داشبورد منتقل میشوید")
                    window.location = "{{ url('') }}" + response
                },
                function(response) {
                    console.log(response);
                    show_error(response)
                    hide_loading()
                }
            )
        }
    </script>
@endsection

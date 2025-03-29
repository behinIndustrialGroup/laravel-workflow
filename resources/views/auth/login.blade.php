@extends('behin-layouts.welcome')

@section('content')
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <img src="{{ url('public/behin/logo.png') . '?' . config('app.version') }}" class="col-sm-12" alt="">
            </div>
            <div class="card-body">
                <form action="javascript:void(0)" method="post" id="login-form">
                    @csrf
                    <input type="hidden" name="remember" value="1">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="email" placeholder="موبایل">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-phone"></span>
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
                    <button type="submit" class="btn btn-primary col-sm-12" onclick="submit()">ورود</button>
                </div>
                {{-- <hr>
            <div class="center-align" style="text-align: center">
                <a href="{{ route('register') }}" class="text-center">صفحه ثبت نام</a>
            </div> --}}
                {{-- <hr>
            <div class="center-align" style="text-align: center">
                <a href="{{ route('password.request') }}" class="text-center">فراموشی رمز</a>
            </div> --}}
                <a referrerpolicy='origin' target='_blank'
                    href='https://trustseal.enamad.ir/?id=573760&Code=xxw9WMjEmu7Jif319UJnJYlBcrY3BgYa'><img
                        referrerpolicy='origin'
                        src='https://trustseal.enamad.ir/logo.aspx?id=573760&Code=xxw9WMjEmu7Jif319UJnJYlBcrY3BgYa'
                        alt='' style='cursor:pointer' code='xxw9WMjEmu7Jif319UJnJYlBcrY3BgYa'></a>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        @if (auth()->id())
            show_message("شما قبلا وارد شده اید")
            show_message("به صفحه داشبورد منتقل میشوید")
            window.location = "{{ url('admin') }}"
        @endif
        function submit() {
            send_ajax_request(
                "{{ route('login') }}",
                $('#login-form').serialize(),
                function(response) {
                    show_message("به صفحه داشبورد منتقل میشوید")
                    window.location = "{{ url('admin') }}"
                },
                function(response) {
                    // console.log(response);
                    show_error(response)
                    hide_loading();
                }
            )
        }
    </script>
@endsection

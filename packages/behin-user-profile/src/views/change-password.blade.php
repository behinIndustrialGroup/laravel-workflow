@extends('behin-layouts.app')

@section('title')
    کاربران
@endsection

@section('content')
    <form action="javascript:void(0)" method="POST" id="change-pass-form">
        @csrf
        {{ method_field('put') }}
        <section class="row pt-2">
            <section class="col-12 col-md-6 my-2">
                <div class="form-group">
                    <label for="new_password">رمز عبور جدید :</label>
                    <input type="text" class="form-control form-control-sm my-2" name="new_password" id="new_password">
                </div>
            </section>
            <section class="col-12 m-1">
                <button class="btn btn-primary btn-sm" onclick="change_pass()">ثبت</button>
            </section>
        </section>
    </form>
@endsection

@section('script')
    <script>
        function change_pass(){
            var form = $('#change-pass-form')[0];
            var data = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('user-profile.update-password') }}",
                data,
                function(response){
                    show_message(response)
                }
            )
        }
    </script>
@endsection

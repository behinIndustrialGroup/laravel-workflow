<div class="col-sm-12">
    @isset ($error)
        <div class="alert alert-danger">{{$error}}</div>
    @endisset
    @isset ($message)
        <div class='alert alert-success'>{{$message}}</div>
    @endisset

    <div class="alert alert-danger error-div" style="display: none"></div>
    <div class="alert alert-success success-div" style="display: none"></div>
    <script>
        const er = $('.error-div');
        const su = $('.success-div');
        function show_error(msg) {
            su.fadeOut();
            er.html(msg);
            er.fadeIn("slow");
        }
        function show_success(msg) {
            er.fadeOut();
            su.html(msg);
            su.fadeIn("slow");
        }
    </script>
</div>
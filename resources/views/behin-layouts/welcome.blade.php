<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="csrf-token" content="{{ csrf_token() }}">
	
		<title>@yield('title')</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/plugins/font-awesome/css/font-awesome.min.css')  . '?' . config('app.version') }}">
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/dist/css/adminlte.min.css')  . '?' . config('app.version') }}">
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')  . '?' . config('app.version') }}">
		<!-- Google Font: Source Sans Pro -->
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
		<!-- bootstrap rtl -->
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/dist/css/bootstrap-rtl.min.css')  . '?' . config('app.version') }}">
		<!-- template rtl version -->
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/dist/css/custom-style.css')  . '?' . config('app.version') }}">
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/dist/css/custom.css')  . '?' . config('app.version') }}">
		<link rel="stylesheet" href="{{ url('public/behin/behin-dist/plugins/select2/select2.min.css')  . '?' . config('app.version') }}">
		<link rel="stylesheet" href="{{ url('public/behin/plugins/persian-datepicker/persian-datepicker.css')  . '?' . config('app.version') }}">
	
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
		@yield('style')
	
		<script src="{{ url('public/behin/behin-dist/plugins/jquery/jquery.min.js')  . '?' . config('app.version') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	
		<script src="{{ url('public/behin/behin-js/ajax.js')  . '?' . config('app.version') }}"></script>
		<script src="{{ url('public/behin/behin-js/dataTable.js')  . '?' . config('app.version') }}"></script>
		<script src="{{ url('public/behin/behin-js/dropzone.js')  . '?' . config('app.version') }}"></script>
		
	</head>
<body>
	
	<div class="limiter">
		@include('behin-layouts.alert')

		<div class="container-login100" style="background-image: url('{{ url('public/behin/login/images/bg-01.jpg') }}');">
			@yield('content')
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
	<!--===============================================================================================-->
	<script src="{{ url('public/behin/behin-js/loader.js')  . '?' . config('app.version') }}"></script>
	<script src="{{ url('public/behin/behin-js/scripts.js')  . '?' . config('app.version') }}"></script>
	<script src="{{ url('public/behin/behin-dist/plugins/select2/select2.full.min.js')  . '?' . config('app.version') }}"></script>
	{{-- <script src="{{ url('public/behin/behin-dist/plugins/persian-datepicker/persian-date.js')  . '?' . config('app.version') }}"></script>
	<script src="{{ url('public/behin/behin-dist/plugins/persian-datepicker/persian-datepicker.js')  . '?' . config('app.version') }}"></script>
	<script src="{{ url('public/behin/behin-dist/dist/js/num2persian/num2persian.js') }}" type="text/javascript"></script> --}}

	<script>
		$('.select2').select2();
		function initial_view(){
                $('.select2').select2();
                $('.select2').css('width', '100%')
                $(".persian-date").persianDatepicker({
                    viewMode: 'year',
                    format: 'YYYY-MM-DD',
                    initialValueType: 'persian'
                });
            }
	</script>
	
	@yield('script')
</body>
</html>
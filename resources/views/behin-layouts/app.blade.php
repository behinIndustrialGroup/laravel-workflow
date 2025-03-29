<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ url('public/behin/logo.ico') . '?' . config('app.version') }}">
    <link rel="manifest" href="{{ url('manifest.json') . '?' . config('app.version') }}">

    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/font-awesome/css/font-awesome.min.css') . '?' . config('app.version') }}">
    <!-- Ionicons -->
    {{-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Theme style -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/dist/css/adminlte.min.css') . '?' . config('app.version') }}">
    <!-- Date Picker -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/datepicker/datepicker3.css') . '?' . config('app.version') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/daterangepicker/daterangepicker-bs3.css') . '?' . config('app.version') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') . '?' . config('app.version') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- bootstrap rtl -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/dist/css/bootstrap-rtl.min.css') . '?' . config('app.version') }}">
    <!-- template rtl version -->
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/dist/css/custom-style.css') . '?' . config('app.version') }}">
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/dist/css/custom.css') . '?' . config('app.version') }}">

    {{-- <link rel="stylesheet" href="{{ url('public/behin/behin-dist/dist/css/custom.css')  . '?' . config('app.version') }}"> --}}
    <link rel="stylesheet" type="text/css"
        href="{{ url('public/behin/behin-dist/plugins/datatables/dataTables.bootstrap4.css') . '?' . config('app.version') }}" />
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/dist/css/dropzone.min.css') . '?' . config('app.version') }}">
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/toastr/toastr.min.css') . '?' . config('app.version') }}">
    {{-- <link rel="stylesheet" href="{{ Url('public/behin/behin-dist/dist/css/persian-datepicker-0.4.5.min.css')  . '?' . config('app.version') }}" /> --}}
    {{-- <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ url('public/behin/behin-dist/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/behin/behin-dist/persian-date-picker/persian-datepicker.css') }}">
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/mapp/css/mapp.min.css') . '?' . config('app.version') }}">
    <link rel="stylesheet"
        href="{{ url('public/behin/behin-dist/plugins/mapp/css/fa/style.css') . '?' . config('app.version') }}">
    @yield('style')

    <script src="{{ url('public/behin/behin-dist/plugins/jquery/jquery.min.js') . '?' . config('app.version') }}"></script>
    {{-- <script type="text/javascript" src="https://cdn.map.ir/web-sdk/1.4.2/js/jquery-3.2.1.min.js"></script> --}}
    <script
        src="{{ url('public/behin/behin-dist/plugins/datatables/jquery.dataTables.js') . '?' . config('app.version') }}">
    </script>
    <script
        src="{{ url('public/behin/behin-dist/plugins/datatables/dataTables.bootstrap4.js') . '?' . config('app.version') }}">
    </script>
    <script src="{{ url('public/behin/behin-dist/persian-date-picker/persian-date.js') . '?' . config('app.version') }}">
    </script>
    <script
        src="{{ url('public/behin/behin-dist/persian-date-picker/persian-datepicker.js') . '?' . config('app.version') }}">
    </script>


    <script src="{{ url('public/behin/behin-dist/plugins/mapp/js/mapp.env.js') . '?' . config('app.version') }}"></script>


    <script src="{{ url('public/behin/behin-js/ajax.js') . '?' . config('app.version') }}"></script>
    <script src="{{ url('public/behin/behin-js/dataTable.js') . '?' . config('app.version') }}"></script>
    <script src="{{ url('public/behin/behin-js/dropzone.js') . '?' . config('app.version') }}"></script>



    @yield('script_in_head')

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        @include('behin-layouts.header')

        @include('behin-layouts.main-sidebar')
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>



        <footer class="main-footer">
            {{-- <strong> &copy; 2018 <a href="http://github.com/hesammousavi/">حسام موسوی</a>.</strong> --}}
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <script
        src="{{ url('public/behin/behin-dist/plugins/bootstrap/js/bootstrap.bundle.min.js') . '?' . config('app.version') }}">
    </script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> --}}
    <script src="{{ url('public/behin/behin-dist/plugins/knob/jquery.knob.js') . '?' . config('app.version') }}"></script>
    <script
        src="{{ url('public/behin/behin-dist/plugins/daterangepicker/daterangepicker.js') . '?' . config('app.version') }}">
    </script>
    <script
        src="{{ url('public/behin/behin-dist/plugins/datepicker/bootstrap-datepicker.js') . '?' . config('app.version') }}">
    </script>
    <script
        src="{{ url('public/behin/behin-dist/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') . '?' . config('app.version') }}">
    </script>
    <script src="{{ url('public/behin/behin-dist/dist/js/adminlte.js') . '?' . config('app.version') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="{{ url('public/behin/behin-dist/plugins/select2/select2.full.min.js') }}"></script>
    {{-- <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
        <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script> --}}
    <script src="{{ url('public/behin/behin-dist/plugins/mapp/js/mapp.min.js') . '?' . config('app.version') }}"></script>
    <script src="{{ url('public/behin/behin-dist/plugins/toastr/toastr.min.js') . '?' . config('app.version') }}"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>
    <script>
        const beamsClient = new PusherPushNotifications.Client({
            instanceId: "{{ config('broadcasting.pusher.instanceId') }}",
        });
        const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
            url: "{{ url('/pusher/beams-auth') }}"
        });

        beamsClient.getUserId()
            .then(userId => {
                if (!userId) {
                    beamsClient.start().then(() => {
                        const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
                            url: "{{ url('/pusher/beams-auth') }}"
                        });
                        beamsClient.setUserId("{{ config('broadcasting.pusher.prefix_user') }}{{ Auth::id() }}",
                            beamsTokenProvider)
                    })
                }else{
                    console.log('User ID:', userId);
                }
            })
            .catch(console.error);
    </script>
    <script>
        document.getElementById('notification-btn').addEventListener('click', () => {
            checkNotificationPermission();
        });

        function checkNotificationPermission() {
            if (!('Notification' in window)) {
                alert('این مرورگر از نوتیفیکیشن پشتیبانی نمی‌کند.');
                return;
            }

            if (Notification.permission === 'granted') {
                new Notification('نوتیفیکیشن فعال است', {
                    body: 'شما قبلاً مجوز داده‌اید!',
                    icon: '{{ url('public/behin/logo.ico') }}'
                });
            } else if (Notification.permission === 'denied') {
                alert('شما مجوز نوتیفیکیشن را رد کرده‌اید. لطفاً از تنظیمات مرورگر آن را فعال کنید.');
            } else {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        new Notification('متشکریم!', {
                            body: 'شما نوتیفیکیشن را فعال کردید.'
                        });
                    }
                });
            }
        }
    </script>
    <script>
        function logout() {
            beamsClient.stop().catch(console.error);
            window.location = "{{ route('logout') }}"
        }
    </script>



    <script>
        function initial_view() {
            $('.select2').select2();
            $('.select2').css('width', '100%')
            $(".persian-date").persianDatepicker({
                viewMode: 'day',
                initialValue: false,
                format: 'YYYY-MM-DD',
                initialValueType: 'persian',
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical',
                        locale: 'fa'
                    }
                }
            });
        }
    </script>

    <script src="{{ url('public/behin/behin-js/loader.js') . '?' . config('app.version') }}"></script>
    <script src="{{ url('public/behin/behin-js/scripts.js') . '?' . config('app.version') }}"></script>
    @yield('script')
    </div>


</body>

</html>

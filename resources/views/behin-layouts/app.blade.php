<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $profile = \UserProfile\Models\UserProfile::where('user_id', auth()->id())->first();
        $headerColor = $profile->header_background ?? '#1976d2';
        $sidebarColor = $profile->sidebar_background ?? '#212121';
    @endphp
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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <!-- Material Icons اضافه -->
    <link href="{{ url('public/behin/behin-dist/dist/css/icon.css') . '?' . config('app.version') }}" rel="stylesheet">

    <!-- استایل سفارشی متریال -->
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .btn {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .main-header.navbar {
            background-color: {{ $headerColor }} !important;
        }

        .main-sidebar {
            border-top-right-radius: 12px;
            background-color: {{ $sidebarColor }} !important;
        }

        .main-footer {
            background-color: #fff;
            border-top: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        /* جدول‌ها کمی متریال‌تر */
        table.dataTable {
            border-radius: 8px;
            overflow: hidden;
        }

        table.dataTable thead {
            background-color: #eeeeee;
        }

        .more-text {
            display: none;
        }

        .show-more-btn {
            cursor: pointer;
            color: #1976d2;
            font-size: 14px;
            vertical-align: middle;
        }

        .show-more-btn:hover {
            text-decoration: underline;
        }
    </style>
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

    <script>
        window.appUrl = "{{ env('APP_URL') }}";
    </script>
    <script src="{{ url('public/behin/behin-js/ajax.js') . '?' . config('app.version') }}"></script>
    <script src="{{ url('public/behin/behin-js/dataTable.js') . '?' . config('app.version') }}"></script>
    <script src="{{ url('public/behin/behin-js/dropzone.js') . '?' . config('app.version') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script
        src="{{ url('public/behin/behin-dist/plugins/autonumeric/autoNumeric.min.js') . '?' . config('app.version') }}">
    </script>



    @yield('script_in_head')

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        @include('behin-layouts.header', ['headerColor' => $headerColor])

        @include('behin-layouts.main-sidebar', ['sidebarColor' => $sidebarColor])
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    @if (!isset($disableBackBtn))
                        <div class="card">
                            <div class="card-header">
                                <a href="javascript:history.back()" class="btn btn-outline-primary float-left">
                                    <i class="fa fa-arrow-left"></i> {{ trans('fields.Back') }}
                                </a>
                            </div>
                        </div>
                    @endisset
            </div>
            <div class="container-fluid p-2">
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
{{-- <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
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
                        beamsClient.setUserId(
                            "{{ config('broadcasting.pusher.prefix_user') }}{{ Auth::id() }}",
                            beamsTokenProvider)
                    })
                } else {
                    console.log('User ID:', userId);
                }
            })
            .catch(console.error);
    </script>
    <script>
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
    </script> --}}
<script>
    function logout() {
        // beamsClient.stop().catch(console.error);
        window.location = "{{ route('logout') }}"
    }
</script>



<script>
    function runAfterTableReady(selector, callback) {
        let checkExist = setInterval(function() {
            let $table = $(selector);
            if ($table.length && $table.find('tbody tr').length > 0) {
                clearInterval(checkExist);
                callback();
            }
        }, 100); // هر 100 میلی‌ثانیه چک می‌کنه
    }

    // استفاده:
    runAfterTableReady('table', initial_view);



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
        $('.timepicker').timepicker({
            timeFormat: 'HH:mm', // فرمت 24 ساعته
            interval: 1, // نمایش با فاصله 5 دقیقه‌ای
            minTime: '00:00',
            maxTime: '23:55',
            dynamic: true,
            dropdown: true,
            scrollbar: true
        });
        AutoNumeric.multiple('.formatted-digit', {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 0,
            unformatOnSubmit: true
        });

            $('.show-less').each(function() {
                let $cell = $(this);

                // اگر دکمه یا اسپن توی همین لحظه وجود داره یعنی قبلا پردازش شده
                if ($cell.find('.toggle-btn').length) return;

                let originalHtml = $cell.html();
                let textOnly = $cell.clone().children().remove().end().text()
                    .trim(); // حذف تگ‌ها برای شمارش دقیق

                if (textOnly.length > 25) {
                    let shortText = textOnly.substr(0, 25);

                    $cell.html(`
            <span class="short-text">${shortText}</span>
            <span class="full-text" style="display:none;">${originalHtml}</span>
            <button class="toggle-btn show-more-btn material-icons" style="border:none;background:none;cursor:pointer;">more_horiz</button>
        `);
                }
            });

            $(document).on('click', '.toggle-btn', function() {
                let $cell = $(this).closest('td');
                $cell.find('.short-text, .full-text').toggle();
                $(this).text($(this).text() === 'more_horiz' ? 'expand_less' : 'more_horiz');
            });
    }
</script>

<script src="{{ url('public/behin/behin-js/loader.js') . '?' . config('app.version') }}"></script>
<script src="{{ url('public/behin/behin-js/scripts.js') . '?' . config('app.version') }}"></script>
@yield('script')
</div>


</body>

</html>

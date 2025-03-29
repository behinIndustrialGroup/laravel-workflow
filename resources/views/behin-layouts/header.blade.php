<?php
use App\CustomClasses\Access;
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
        {{-- <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">خانه</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">تماس</a>
      </li> --}}
        @if (access('send-sms'))
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('admin/send-sms') }}" class="nav-link btn btn-default">
                    ارسال پیامک
                </a>
            </li>
        @endif
        {{-- <button id="notification-btn" class="btn btn-sm btn-warning">فعال کردن نوتیفیکیشن</button> --}}
    </ul>

    <!-- SEARCH FORM -->
    {{-- <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="جستجو" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </form> --}}

    <!-- Right navbar links -->
    <ul class="navbar-nav mr-auto">
        <!-- Messages Dropdown Menu -->
        {{-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-comments-o"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
                <a href="{{ url('admin/messages/list') }}" class="dropdown-item dropdown-footer">نمایش تماام پیام ها</a>
            </div>
        </li> --}}
        <li class="mr-2">
            <a href="{{ url('admin') }}" class="btn btn-sm btn-warning">
                <i class="fa fa-home"></i>
            </a>
        </li>

        @include('TodoListViews::partial-views.todo-list-icon')

        @include('UserProfileViews::partial-views.user-profile-icon')

        <li class="mr-2">
            <button class="btn btn-sm btn-danger" onclick="logout()"><i class="fa fa-power-off"></i></button>
        </li>
    </ul>
</nav>

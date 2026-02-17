<?php
use App\CustomClasses\Access;
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand" style="background-color: {{ $headerColor ?? '#263238' }}; color: #fff; border-bottom: none;">

    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center">
        <!-- Menu Toggle -->
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#">
                <i class="material-icons">menu</i>
            </a>
        </li>

        <!-- Send SMS -->
        @if (access('send-sms'))
            <li class="nav-item">
                <a href="{{ url('admin/send-sms') }}" class="btn btn-sm btn-outline-light ms-2">
                    <i class="material-icons" style="font-size:18px;">sms</i>
                    ارسال پیامک
                </a>
            </li>
        @endif

        <!-- Test Notification -->
        <li class="nav-item d-none d-md-block">
            <a href="{{ route('send-notification') }}" class="btn btn-sm btn-warning">
                <i class="fa fa-bell"></i>
                تست نوتیفیکیشن
            </a>
        </li>
        <li class="nav-item d-md-block">
            <button type="button" class="btn btn-sm btn-warning" onclick="checkNotificationPermission()">
                <i class="fa fa-bell"></i>
            </button>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ms-auto align-items-center" >

        <!-- Refresh -->
        <li class="mr-2">
            <button type="button" class="btn btn-sm btn-outline-light" onclick="window.location.reload()">
                <i class="fa fa-refresh"></i>
                {{ __('Refresh') }}
            </button>
        </li>

        <!-- Home -->
        <li class="mr-2">
            <a href="{{ url('admin') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-home"></i>
            </a>
        </li>

        <!-- Todo List -->
        @include('TodoListViews::partial-views.todo-list-icon')

        <!-- User Profile -->
        @include('UserProfileViews::partial-views.user-profile-icon')

        <!-- Logout -->
        <li class="mr-2">
            <button class="btn btn-sm btn-danger" onclick="logout()">
                <i class="fa fa-sign-out"></i>
            </button>
        </li>
    </ul>
</nav>

<!-- Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    .navbar .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .navbar .btn-outline-light:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }
    .navbar .btn-warning {
        color: #000;
        font-weight: 600;
    }
    .navbar .nav-link:hover {
        background: rgba(255,255,255,0.08);
        border-radius: 8px;
    }
    .navbar-expand{
        justify-content:space-between !important;
    }
</style>

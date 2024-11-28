<?php

use BehinInit\App\Http\Controllers\AccessController;

if (!function_exists('access')) {
    function access($method_name) {
        return (new AccessController($method_name))->check();
    }
}
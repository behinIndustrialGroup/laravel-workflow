<?php

namespace BehinLogging\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoggingController extends Controller
{
    public static function info($channel, $log){
        $channel_ar = config("logging.channels.$channel");
        if(!$channel){
            return;
        }
        Log::channel($channel)->info($log);
    }
}


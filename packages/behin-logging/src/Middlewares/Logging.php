<?php

namespace BehinLogging\Middlewares;

use BehinInit\App\Http\Controllers\AccessController;
use BehinLogging\Controllers\LoggingController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Logging
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $logChannel = 'user_' . $user->id;

            // Dynamically create a log channel for the user
            Log::build([
                'driver' => 'single',
                'path' => storage_path("logs/{$logChannel}.log"),
            ])->info('User Action: ', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => $request->method() . ' ' . $request->path(),
                'params' => $request->all(),
                'timestamp' => now()
            ]);
        }

        return $response;
    }
}

<?php

namespace BehinInit\App\Http\Middleware;

use BehinInit\App\Http\Controllers\AccessController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Access
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null $method
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $method = null)
    {
        if(!Auth::id()){
            return abort(403, 'ابتدا وارد شوید');
        }
        $user = Auth::user();
        if($user->login_with_ip){
            if($user->valid_ip != $request->ip()){
                return abort(403, "آیپی شما معتبر نیست");
            }
        }
        if(Auth::id() != 1){
            // return abort(403, "در حال آپدیت . چند لحظه منتظر بمانید");
        }
        $target = $method ?? $request->route()->uri();
        $a = new AccessController($target);
        if(!$a->check()){
            return abort(403, "Forbidden For Route: " . $target);
        }

        return $next($request);
    }
}

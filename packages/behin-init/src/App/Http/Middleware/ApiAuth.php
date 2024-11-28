<?php

namespace App\Http\Middleware;

use App\Enums\EnumsEntity;
use App\Models\User;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ApiAuth
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log::info($request->ip());
        $user = User::where('valid_ip', $request->ip())->first();
        Log::channel('irngv_poll_api_activity')->info("Request Received From IP=" . $request->ip());
        if (!$user) {
            Log::channel('irngv_poll_api_activity')->info(EnumsEntity::irngv_api_msg_code[6]);
            Log::channel('irngv_poll_api_activity')->info("-----------END OF LOG-----------");
            return $this->jsonResponse(EnumsEntity::irngv_api_msg_code[6], 403, [], 6);
        }

        Log::channel('irngv_poll_api_activity')->info("Received username=". $request->username);
        if ($user->email != $request->username) {
            Log::channel('irngv_poll_api_activity')->info(EnumsEntity::irngv_api_msg_code[7]);
            Log::channel('irngv_poll_api_activity')->info("-----------END OF LOG-----------");
            return $this->jsonResponse(EnumsEntity::irngv_api_msg_code[7], 403, [], 7);
        }

        Log::channel('irngv_poll_api_activity')->info("Received password=". $request->password);
        if (!Hash::check($request->password, $user->password)) {
            Log::channel('irngv_poll_api_activity')->info(EnumsEntity::irngv_api_msg_code[7]);
            Log::channel('irngv_poll_api_activity')->info("-----------END OF LOG-----------");
            return $this->jsonResponse(EnumsEntity::irngv_api_msg_code[7], 403, [], 7);
        }
        Auth::loginUsingId($user->id);
        // if(!Auth::attempt([ 'valid_ip' => $request->ip(), 'email' => $request->username, 'password' => $request->password ])){
        //     return $this->jsonResponse("نام کاربری یا رمز عبور صحیح نیست", 403);
        // }
        return $next($request);
    }
}

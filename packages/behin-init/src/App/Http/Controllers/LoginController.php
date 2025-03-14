<?php
namespace BehinInit\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\PushNotificationController;
use Behin\SimpleWorkflow\Controllers\Core\PushNotifications;
use BehinInit\App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $publishResponse = new PushNotificationController([
            'instanceId' => env('PUSHER_INSTANCE_ID'),
            'secretKey' => env('PUSHER_SECRET_KEY')
        ]);
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $beamsToken = $publishResponse->generateToken($userId);
        $user->beams_token = $beamsToken['token'];
        $user->save();
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
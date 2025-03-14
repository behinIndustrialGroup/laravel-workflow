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
        $publishResponse = new PushNotificationController(["instanceId" => "6eb4fa4d-2ab6-4d5e-bd9c-68f5668c732b", "secretKey" => "9924F201697A95F35835AF36734EB0BA50E9B99E7E5484EB00F1D3D52E51B90F"]);
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
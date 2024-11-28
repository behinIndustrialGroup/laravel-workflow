<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class InboxController extends Controller
{
    function get() {
        $newPass = rand(10000000,99999999 );
        $accessToken = PMController::getAccessToken();
        $userId = PMController::getUserId($accessToken, Auth::user()->pm_username);
        PMController::changePass($accessToken, $userId, $newPass);
        return view('test')->with([
            'user' => Auth::user()->pm_username,
            'pass' => $newPass
        ]);
    }
}
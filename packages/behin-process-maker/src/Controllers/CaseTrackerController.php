<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class CaseTrackerController extends Controller
{
    public static function get($caseId) {
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken, 
            "/api/1.0/workflow/home/mycases"
        );
    }
}
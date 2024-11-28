<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StartCaseController extends Controller
{
    private $accessToken;

    public function __construct() {
    }
    function get() {
        $this->accessToken = AuthController::getAccessToken('mkhodroo', 'Mk09376922176');
        return [
            'data'=> CurlRequestController::send(
                $this->accessToken, 
                "/api/1.0/workflow/case/start-cases"
            )
        ]; 
    }

    function form() {
        return view('PMViews::start');        
    }
}
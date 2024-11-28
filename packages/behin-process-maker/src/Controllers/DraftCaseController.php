<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mkhodroo\PMReport\Controllers\TableController;
use SoapClient;

class DraftCaseController extends Controller
{
    private $accessToken;

    public function __construct()
    {
    }
    function getMyCase()
    {
        $this->accessToken = AuthController::getAccessToken();
        $draft =  CurlRequestController::send(
            $this->accessToken,
            "/api/1.0/workflow/home/draft"
        );
        // $r = new Request([
        //     'table_name' => 'application'
        // ]);
        // $application = TableController::getData($r)['results'];
        // $application = collect($application);
        // foreach($draft->data as $data){
        //     $data->APP_DATA = unserialize($application->where('APP_UID', $data->APP_UID)->first()->APP_DATA);
        //     if (isset($data->APP_DATA['MAIN_INFO'])) {
        //         $data->MAIN_INFO = $data->APP_DATA['MAIN_INFO'];
        //     } else {
        //         $data->MAIN_INFO = '';
        //     }
        // }
        return $draft;

    }

    function form()
    {
        return view('PMViews::draft');
    }
}

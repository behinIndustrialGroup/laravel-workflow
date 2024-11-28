<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mkhodroo\PMReport\Controllers\TableController;
use SoapClient;

class DoneCaseController extends Controller
{
    private $accessToken;

    public function __construct()
    {
    }
    function getMyCase()
    {
        $this->accessToken = AuthController::getAccessToken();
        $mycases =  CurlRequestController::send(
            $this->accessToken,
            "/api/1.0/workflow/home/mycases"
        );

        // $search =  CurlRequestController::send(
        //     $this->accessToken,
        //     "/api/1.0/workflow/home/search"
        // );
        // $r = new Request([
        //     'table_name' => 'application'
        // ]);
        // $application = TableController::getData($r)['results'];
        // $application = collect($application);
        // // $mycases->data = array_merge($mycases->data, $search->data);
        // foreach ($mycases->data as $data) {
        //     $data->APP_STATUS = trans($data->APP_STATUS);
        //     if (!isset($data->PENDING)) {
        //         $data->PENDING = $data->THREAD_TASKS;
        //     }
        //     $data->APP_DATA = unserialize($application->where('APP_UID', $data->APP_UID)->first()->APP_DATA);
        //     if (isset($data->APP_DATA['MAIN_INFO'])) {
        //         $data->MAIN_INFO = $data->APP_DATA['MAIN_INFO'];
        //     } else {
        //         $data->MAIN_INFO = $data->APP_DATA;
        //     }
        //     // $data->FORM = DynaFormController::getHtml($data->PRO_UID, $data->APP_UID, "38427515364e34afc5c10a7052199230", "", "" , $data->APP_DATA );
        //     //کل دیتاها رو از جدول application بخون و فیلد main_info رو از جدول اپلیکیشن پیدا کن سرعت کار میره بالاتر
        //     // $data->MAIN_INFO = (new GetCaseVarsController())->getMainInfoByCaseId($data->APP_UID);
        // }
        return $mycases;
    }

    function form()
    {
        return view('PMViews::done');
    }

    public static function mainForm(Request $r)
    {
        $variable_values = (new GetCaseVarsController())->getByCaseId($r->caseId);
        $mainFormId = config('pm_config.process')[$r->processId]['mainFormId'];
        if(!$mainFormId){
            return response(trans("There is no main form id in pm_config file"), 500);
        }
        return DynaFormController::getHtml(
            $r->processId,
            $r->caseId,
            $mainFormId,
            "",
            "",
            $variable_values
        );
    }
}

<?php

namespace Behin\PMCaseNumbering\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request;

class ApiKeyController extends Controller
{
    public static function checkApiKey($pro_id, $api_key)
    {
        $row = CaseNumberingController::getOrCreate($pro_id);
        if($row->api_key === $api_key){
            return  null;
        }
        return response()->json([
            'status' => 403,
            'msg' => 'api key is not valid'
        ], 403);
    }

}

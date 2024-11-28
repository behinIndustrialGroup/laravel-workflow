<?php

namespace Behin\PMCaseNumbering\Controllers;

use App\Http\Controllers\Controller;
use Behin\PMCaseNumbering\Models\PMCaseNumbering;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewNummberController extends Controller
{
    public static function getNewNumber(Request $request)
    {
        // CHECK API KEY IS VALID 
        $c = ApiKeyController::checkApiKey($request->pro_id, $request->api_key);
        if($c){
            return $c;
        }

        // CREATE A ROW IF THERE IS NO PRO_ID RECORD
        $number = CaseNumberingController::getOrCreate($request->pro_id);

        $number->count = $number->count +1;
        $number->save();
        return response()->json([
            'status' => 200,
            'count' => $number->count
        ]);
    }

    

}

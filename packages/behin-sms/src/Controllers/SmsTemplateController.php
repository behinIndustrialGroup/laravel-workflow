<?php

namespace Mkhodroo\SmsTemplate\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsTemplateController extends Controller
{
    public static function getViewById($sms_id){
        switch($sms_id){
            case "123456";
                return view('SmsTempView::test');
                break;
            case "14030101";
                return view('SmsTempView::happy-new-year');
                break;
        }
    }

    public static function send( SendSmsController $sms ,$sms_id, $to, $params = null) {
        $body = self::getViewById($sms_id); 
        if(!$body){
            return response("no view founded",400);
        }
        $params = $params ? unserialize($params): null ;
        $body = $body->with(['params' => $params])->render();  
        $sms->send($to, $body);
        
    }
}

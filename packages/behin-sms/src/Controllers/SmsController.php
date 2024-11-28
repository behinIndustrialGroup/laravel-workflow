<?php

namespace Behin\Sms\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Melipayamak\MelipayamakApi;

class SmsController extends Controller
{
    private $url;
    private $user;
    private $pass;
    private $org;

    public function __construct() {
        
    }
    public static function send($to, $text)
    {
        $username = '09376265059';
        $password = '1566bf7b-70fb-4e41-a635-0ea0612a7d28';
        $api = new MelipayamakApi($username,$password);
        $smsRest = $api->sms();
        $to = '09376922176';
        $from = '50004001265059';
        $response = $smsRest->send($to, $from, $text, false);
        $json = json_decode($response);
        return $json;
    }

    public function sendByBaseNumber($to, $bodyId, array $text)
    {
        $username = '09376265059';
        $password = '1566bf7b-70fb-4e41-a635-0ea0612a7d28';
        $api = new MelipayamakApi($username,$password);
        $smsRest = $api->sms();
        $response = $smsRest->sendByBaseNumber($text, $to, $bodyId);
        $json = json_decode($response);
        return $json;
    }

}

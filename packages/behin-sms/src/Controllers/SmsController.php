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
    public static function send($to, $code)
    {
        $curl = curl_init();
        $postFields = array(
            "mobile" => $to,
            "templateId" => 187709,
            "parameters" => array([
                "name" => "CODE",
                "value" => $code
            ])
        );
        $postFields = json_encode($postFields);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: text/plain',
                'x-api-key: '.env('SMS_IR_API_KEY')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public static function sendByTemp($to, $tempCode, array $parameter)
    {
        $curl = curl_init();
        $postFields = array(
            "mobile" => $to,
            "templateId" => $tempCode,
            "parameters" => $parameter
        );
        $postFields = json_encode($postFields);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: text/plain',
                'x-api-key: '.env('SMS_IR_API_KEY')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }


}

<?php

namespace Behin\Sms\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Melipayamak\MelipayamakApi;
use Illuminate\Support\Facades\Http;

class ArsanSmsController extends Controller
{
    private $url;
    private $user;
    private $pass;
    private $org;

    public function __construct() {}

    public static function  send($to, $msg)
    {
        $id = time();
        $data = [
            "Smsbody"      => "$msg",
            "Mobiles"      => ["$to"],
            "Id"           => "$id",
            "SenderNumber" => env('ARSANSMS_SENDERNUMBER'),
            "UserName"     => env('ARSANSMS_USERNAME'),
            "Password"     => env('ARSANSMS_PASSWORD'),
            "DomainName"   => env('ARSANSMS_DOMAINNAME')
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->withoutVerifying()->post(env('ARSANSMS_SENDURL'), $data);

        Log::info('SMS API Request', (array) $response->body());

        return $response->json();
    }
}

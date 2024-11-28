<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CurlRequestController extends Controller
{
    private static $apiServer;

    public static function send($accessToken, $url, $data = null)
    {
        self::$apiServer = str_replace('https', 'http', env('PM_SERVER'));
        // Log::info(self::$apiServer . $url);

        $ch = curl_init(self::$apiServer . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $results = json_decode(curl_exec($ch));

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode != 200) {
            if (isset($results) and isset($results->error))
                Log::info("Error code: {$results->error->code}\nMessage: {$results->error->message}\n");
            else
                Log::info("Error: HTTP status code: $statusCode\n");
        } else {
            return $results;
        }
    }

    public static function post($accessToken, $url, $data = null)
    {
        self::$apiServer = str_replace('https', 'http', env('PM_SERVER'));
        $ch = curl_init(self::$apiServer . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $results = json_decode(curl_exec($ch));

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode != 200) {
            if (isset($results) and isset($results->error)){
                Log::info("Error code: {$results->error->code}\nMessage: {$results->error->message}\n");
                return response($results->error->message, $results->error->code);
            }
            else
                Log::info("Error: HTTP status code: $statusCode\n");
        } else {
            return $results;
        }
    }

    public static function put($accessToken, $url, $data = null)
    {
        // Log::info(json_encode($data));
        self::$apiServer = str_replace('https', 'http', env('PM_SERVER'));
        $ch = curl_init(self::$apiServer . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
        $results = json_decode(curl_exec($ch));

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode != 200) {
            if (isset($results) and isset($results->error)){
                Log::info("Error code: {$results->error->code}\nMessage: {$results->error->message}\n");
                return response($results->error->message, $results->error->code);
            }
            else
                Log::info("Error: HTTP status code: $statusCode\n");
        } else {
            return $results;
        }
    }

    public static function delete($accessToken, $url)
    {
        self::$apiServer = str_replace('https', 'http', env('PM_SERVER'));
        $ch = curl_init(self::$apiServer . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        // curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
        $results = json_decode(curl_exec($ch));

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode != 200) {
            if (isset($results) and isset($results->error)){
                Log::info("Error code: {$results->error->code}\nMessage: {$results->error->message}\n");
                return response($results->error->message, $results->error->code);
            }
            else
                Log::info("Error: HTTP status code: $statusCode\n");
        } else {
            return $results;
        }
    }
}

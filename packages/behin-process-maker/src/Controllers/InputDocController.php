<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use CURLFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class InputDocController extends Controller
{
    private $accessToken;

    public static function list($app_uid)
    {
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken,
            "/api/1.0/workflow/cases/$app_uid/input-documents"
        );
    }

    public static function get($app_uid, $app_doc_uid)
    {
        $accessToken = AuthController::getAccessToken();
        return CurlRequestController::send(
            $accessToken,
            "/api/1.0/workflow/cases/$app_uid/input-document/$app_doc_uid"
        );
    }

    public static function upload($file, $taskId, $caseId, $inputDocId = null, $userId, $docFieldName)
    {
        $path = $file;
        $params = array(
            'ATTACH_FILE'  => (phpversion() >= "5.5") ? new CurlFile($path, $file->getClientMimeType(), $file->getClientOriginalName()) : '@' . $path,
            'APPLICATION'  => $caseId,
            'INDEX'        => 1,
            'USR_UID'      => $userId,
            'DOC_UID'      => $inputDocId,
            'APP_DOC_TYPE' => 'INPUT',
            'APP_DOC_FIELDNAME' => $docFieldName,
            'APP_DOC_FILENAME' => $file. '.' . $file->getClientOriginalExtension(),
            'TITLE'        => $docFieldName,
            'COMMENT'      => "Scanned ID document"
        );
        ob_flush();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://pmaker.altfuel.ir/sysworkflow/en/neoclassic/services/upload');
        // curl_setopt($ch, CURLOPT_VERBOSE, 1);  //Uncomment to debug
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 1); //Uncomment for SSL
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 1); //Uncomment for SSL
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
        $accessToken = AuthController::getAccessToken();

        $path = $file;
        $type = $file->getClientMimeType();

        $aVars = array(
            'inp_doc_uid'     => $inputDocId,
            'tas_uid'         => $taskId,
            'app_doc_comment' => '',
            'form'            => (phpversion() >= "5.5") ? new CURLFile($path, $type) : '@' . $path
        );
        return CurlRequestController::send(
            $accessToken,
            "/api/1.0/workflow/cases/$caseId/input-document"
        );
    }
}

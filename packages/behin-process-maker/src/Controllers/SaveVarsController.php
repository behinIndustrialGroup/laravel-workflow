<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use BehinFileControl\Controllers\FileController;
use BehinProcessMaker\Models\PmVars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;

class SaveVarsController extends Controller
{
    public static function save($process_id, $case_id, $key, $value) {
        PmVars::updateOrCreate(
            [
                'process_id' => $process_id,
                'case_id' => $case_id,
                'key' => $key
            ],
            [
                'value' => $value
            ]
            );
    }

    public static function saveDoc($process_id, $case_id, $key, $value) {
        $value = FileController::store(
            $value,
            'pm-docs'
        );
        if($value['status'] == 200){
            PmVars::create(
                [
                    'process_id' => $process_id,
                    'case_id' => $case_id,
                    'key' => $key,
                    'value' => $value['dir']
                ]
            );
            return ;
        }
        return response($value['message'], $value['status']);
        
    }
}

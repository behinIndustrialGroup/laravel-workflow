<?php

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use BehinFileControl\Controllers\FileController;
use BehinProcessMaker\Models\PmVars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;

class DeleteVarController extends Controller
{
    public static function deleteDoc(Request $request, DynaFormController $dynaFormController) {
        PmVars::where(
            [
                'id' => $request->id,
                'process_id' => $request->processId,
                'case_id' => $request->caseId
            ]
        )->first()->delete();
        return $dynaFormController->get($request);
    }
}

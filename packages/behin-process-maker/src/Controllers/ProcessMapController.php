<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use BehinProcessMaker\Models\PMCase;
use SoapClient;

class ProcessMapController extends Controller
{
    public static function getCaseProcessMap($caseId){
        $accessToken = AuthController::getAccessToken();
        echo "<pre>";
        $a =  CurlRequestController::send(
            $accessToken,
            "/api/1.0/workflow/cases/$caseId/tasks"
        );
        echo "<table>";
        foreach ($a as $a) {
            echo "<tr>";
            echo "<td>";
            echo $a->tas_title;
            echo "</td>";
            echo "<td>";
            echo $a->usr_firstname . ' ' . $a->usr_lastname . "($a->usr_username)";
            echo "</td>";
            echo "<td>";
            echo $a->status;
            echo "</td>";
            foreach ($a->delegations as $del) {
                if ($del->del_init_date != 'Case not started yet') {
                    echo "<td>";
                    echo $del->del_init_date;
                    echo "</td>";
                    echo "<td>";
                    echo $del->del_finish_date;
                    echo "</td>";
                    echo "<td>";
                    echo $del->del_duration;
                    echo "</td>";
                }
            }
            echo "<tr>";
        }
        echo "</table>";
    }
}


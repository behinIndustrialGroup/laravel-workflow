<?php 

namespace BehinProcessMaker\Controllers\User;

use App\Http\Controllers\Controller;
use BehinProcessMaker\Controllers\AuthController;
use BehinProcessMaker\Controllers\CurlRequestController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateUserController extends Controller
{
    public static function create(Request $r){
        $r->validate([
            'username' => ['required'],
            'firstname' => ['required'],
            'lastname' => ['required'],
        ]);
        $exp_date = Carbon::now()->addYear(5)->format('Y-m-d');
        $pass = Str::random(10);
        $result =  CurlRequestController::post(
            AuthController::getAccessToken(),
            "/api/1.0/workflow/user",
            array(
                'usr_username' => $r->username,
                'usr_firstname' => $r->firstname,
                'usr_lastname' => $r->lastname,
                'usr_email' => $r->username .'@' . 'test.ir',
                'usr_due_date' => $exp_date,
                'usr_status' => 'ACTIVE',
                'usr_role' => 'PROCESSMAKER_OPERATOR',
                'usr_new_pass' => $pass,
                'usr_cnf_pass' => $pass,
            )
        );
        return $result;
    }
}


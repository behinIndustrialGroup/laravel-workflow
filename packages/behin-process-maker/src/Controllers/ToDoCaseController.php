<?php 

namespace BehinProcessMaker\Controllers;

use App\Http\Controllers\Controller;
use ArrayObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mkhodroo\PMReport\Controllers\TableController;

class ToDoCaseController extends Controller
{

    function getMyCase()
    {        
        $inbox =  CurlRequestController::send(
            AuthController::getAccessToken(),
            "/api/1.0/workflow/home/todo"
        );
        $unassigns = UnassignedCaseController::getMyCase();
        foreach($unassigns?->data as $unassign){
            $inbox->data[] = $unassign;
        }
        return $inbox;
    }

    function form()
    {
        return view('PMViews::todo');
    }
}
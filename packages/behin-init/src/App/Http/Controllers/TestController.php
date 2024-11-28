<?php 

namespace BehinInit\App\Http\Controllers;

use App\Http\Controllers\Controller;
use BehinInit\App\Models\Access;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\Method;

class TestController extends Controller
{

    function create() {
        return "test";
    }
}
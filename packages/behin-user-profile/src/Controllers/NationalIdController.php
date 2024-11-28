<?php

namespace UserProfile\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use UserProfile\Models\UserProfile;

class NationalIdController extends Controller
{
    public function store(Request $request){
        UserProfile::updateOrCreate(
            [
                'user_id' => Auth::id()
            ],
            [
                'national_id' => $request->national_id
            ]
        );
    }
}

<?php

namespace UserProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use UserProfile\Models\UserProfile;

class UserProfileController extends Controller
{


    public function index(){
        $user = Auth::user();
        return view('UserProfileViews::index')->with([
            'user' => $user,
            'userProfile' => self::getByUserId($user->id)
        ]);
    }

    public static function getByUserId($user_id){
        return UserProfile::where('user_id', $user_id)->first();
    }
}


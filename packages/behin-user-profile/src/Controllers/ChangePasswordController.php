<?php

namespace UserProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function edit(){
        return view('UserProfileViews::change-password')->with([
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request){
        $user = Auth::user();
        $newPassword = Hash::make($request->new_password);
        User::where('id', $user->id)->update(['password' => $newPassword]);
        return response(trans("ok"));
    }


}

<?php

namespace UserProfile\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use UserProfile\Models\UserProfile;

class ThemeColorController extends Controller
{
    public function store(Request $request)
    {
        UserProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'header_background' => $request->header_background,
                'sidebar_background' => $request->sidebar_background,
            ]
        );
    }
}

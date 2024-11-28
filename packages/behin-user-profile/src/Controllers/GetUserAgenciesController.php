<?php

namespace UserProfile\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mkhodroo\AgencyInfo\Controllers\GetAgencyController;

class GetUserAgenciesController extends Controller
{

    public static function get(Request $request){
        $agencies = GetAgencyController::getAllByKeyValue(['national_id', 'mobile'], [$request->national_id, $request->mobile]);
        return view('UserProfileViews::partial-views.agencies-list-table', compact('agencies'));
    }

    public static function getLocation($parent_id){
        return view('UserProfileViews::partial-views.location', compact('parent_id'));
    }

}

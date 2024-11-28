<?php

namespace BehinUserRoles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use BehinUserRoles\Controllers\GetRoleController;
use BehinUserRoles\Models\User;

class UserController extends Controller
{

    public function index($id)
    {
        if($id == 'all'):
            $users = User::get();
            return view('URPackageView::user.all')->with(['users' => $users]);
        else:

            return view('URPackageView::user.edit')->with([
                'user' => User::find($id),
                'roles' => GetRoleController::getAll()
            ]);
        endif;
    }


    public function ChangePass(Request $request, $id)
    {
        User::where('id', $id)->update([ 'password' => Hash::make($request->pass) ]);
        return redirect()->back();
    }

    function changePMUsername(Request $r, $id) {
        User::where('id', $id)->update(['pm_username' => $r->pm_username]);
        return redirect()->back();
    }

    public function ChangeIp(Request $r, $user_id)
    {
        User::where('id',$user_id)->update([ 'valid_ip' => $r->valid_ip ]);
        return redirect()->back();
    }

    public function changeShowInReport(Request $r, $id){
        if(isset($r->showInReport))
            $showInReport = true;
        else
            $showInReport = false;
        User:: where('id', $id)->update([ 'showInReport' => $showInReport ]);
        return redirect()->back();
    }


}

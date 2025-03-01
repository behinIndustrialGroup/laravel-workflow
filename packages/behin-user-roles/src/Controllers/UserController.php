<?php

namespace BehinUserRoles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use BehinUserRoles\Controllers\GetRoleController;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use BehinUserRoles\Models\UserDepartment;

class UserController extends Controller
{
    public static function getAll() {
        return User::get();
    }
    public function index($id)
    {
        if($id == 'all'):
            $users = User::get();
            return view('URPackageView::user.all')->with(['users' => $users]);
        else:

            return view('URPackageView::user.edit')->with([
                'user' => User::find($id),
                'roles' => GetRoleController::getAll(),
                'departments' => DepartmentController::getAll($id)
            ]);
        endif;
    }

    public function create()
    {
        $roles = GetRoleController::getAll();
        return view('URPackageView::user.create')->with(['roles' => $roles]);
    }

    public function store(Request $request) {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password)
        ]);
        return redirect()->back()->with('success', 'User created successfully');
    }

    public function update(Request $r, $id) {
        User::where('id', $id)->update([
            'number' => $r->number,
            'name' => $r->name,
            'email' => $r->email
        ]);
        return redirect()->back()->with('success', 'User updated successfully');
    }


    public function ChangePass(Request $request, $id)
    {
        User::where('id', $id)->update([ 'password' => Hash::make($request->pass) ]);
        return redirect()->back()->with('success', 'Password changed successfully');
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

    public function addToDepartment(Request $r, $id){
        $user = User::find($id);
        $department_id = $r->department_id;
        UserDepartment::updateOrCreate([
            'user_id' => $id,
            'department_id' => $department_id
        ]);
        return redirect()->back()->with('success', 'User added to department successfully');
    }

    public function removeFromDepartment(Request $r, $id){
        $user = User::find($id);
        $departmentId = $r->departmentId;
        UserDepartment::where([
            'user_id' => $id,
            'department_id' => $departmentId
        ])->delete();
        return redirect()->back()->with('success', 'User removed from department successfully');
    }

}

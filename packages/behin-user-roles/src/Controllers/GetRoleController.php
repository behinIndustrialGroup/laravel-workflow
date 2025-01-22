<?php

namespace BehinUserRoles\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use BehinInit\App\Http\Controllers\AccessController;
use Illuminate\Http\Request;
use BehinInit\App\Models\Access;
use BehinUserRoles\Models\Role;

class GetRoleController extends Controller
{
    public static function hasNotAccess(){
        $access = new AccessController('User Roles');
        if($access->check()){
            return false;
        }
        return true;
    }
    function listForm() {
        if(self::hasNotAccess()){
            abort(403, 'Access denied');
        }
        $roles = self::getAll();
        return view('URPackageView::roles.list', compact('roles'));
    }

    function list() {
        return [
            'data' => Role::get(),
        ];
    }

    public static function getAll() {
        return Role::get();
    }

    public function show($id) {
        if(self::hasNotAccess()){
            abort(403, 'Access denied');
        }
        $role = self::getById($id);
        $methods = GetMethodsController::getByRoleAccess($role->id);
        return view('URPackageView::roles.show', compact('role', 'methods'));
    }


    function get(Request $r) {
        return view('URPackageView::roles.edit')->with([
            'role' => Role::find($r->id),
            'methods' => GetMethodsController::getByRoleAccess($r->id),
        ]);
    }

    function edit(Request $r) {
        Role::where('id', $r->role_id)->update([
            'name' => $r->name,
        ]);
        foreach(GetMethodsController::getAll() as $method){
            Access::updateOrCreate(
                [
                    'role_id' => $r->role_id,
                    'method_id' => $method->id
                ],
                [
                    'access' => $r->input("$method->id") ? 1 : 0
                ]
            );
        }
        return redirect()->back()->with('success', 'Role updated successfully');
        return response('ok');
    }

    function changeUserRole(Request $r) {
        User::where('id', $r->id)->update([
            'role_id' => $r->role_id
        ]);
        return redirect()->back()->with('success', 'User role updated successfully');
    }

    public static function getByName($name){
        return Role::where('name', $name)->first();
    }

    public static function getById($id){
        return Role::find($id);
    }

    
    public static function getRoleAccess($role_id){
        return Access::where('role_id', $role_id)->get();
    }
    


    public static function copy($id){
        $role = self::getById($id);
        $newRole = $role->replicate();
        $newRole->save();

        foreach (self::getRoleAccess($role->id) as $access) {
            Access::updateOrCreate(
                [
                    'role_id' => $newRole->id,
                    'method_id' => $access->method_id
                ],
                [
                    'access' => $access->access
                ]
            );
        }
        return redirect()->back()->with('success', 'Role copied successfully');
    }
}

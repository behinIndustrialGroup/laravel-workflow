<?php

namespace BehinUserRoles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use BehinUserRoles\Controllers\GetRoleController;
use BehinUserRoles\Models\Department;
use BehinUserRoles\Models\User;

class DepartmentController extends Controller
{
    public static function getAll(){
        return Department::get();
    }

    public static function get($id){
        return Department::findOrFail($id);
    }

    public static function getUserDepartments($userId){
        return User::where('userId', $userId)->get();
    }

    public function index()
    {
        $departments = self::getAll();
        return view('URPackageView::department.index', compact('departments'));
    }

    public function create()
    {
        $departments = self::getAll();
        $managers = UserController::getAll();
        return view('URPackageView::department.create', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $department = new Department();
        $department->name = $request->name;
        $department->manager = $request->manager;
        $department->parent_id = $request->parent_id;
        $department->save();
        return redirect(route('department.index'));
    }

    public function show($id)
    {
        $department = $this->get($id);
        return view('URPackageView::department.show', compact('department'));
    }

    public function edit($id)
    {
        $department = $this->get($id);
        return view('URPackageView::department.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        //
    }


}

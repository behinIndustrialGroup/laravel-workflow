<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Employee_salaries;
use BehinUserRoles\Models\User;
use Illuminate\Http\Request;

class EmployeeSalaryReportController extends Controller
{
    public function index()
    {
        $users = User::orderBy('number')->get();
        $salaries = Employee_salaries::whereIn('user_id', $users->pluck('id'))->get()->keyBy('user_id');

        $users = $users->map(function ($user) use ($salaries) {
            $salary = $salaries->get($user->id);
            $user->insurance_salary = $salary->insurance_salary ?? null;
            $user->real_salary = $salary->real_salary ?? null;

            return $user;
        });

        return view('SimpleWorkflowReportView::Core.EmployeeSalary.index', compact('users'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'insurance_salary' => ['nullable', 'numeric', 'min:0'],
            'real_salary' => ['nullable', 'numeric', 'min:0'],
        ]);

        Employee_salaries::updateOrCreate(
            ['user_id' => $validated['user_id']],
            [
                'insurance_salary' => $validated['insurance_salary'],
                'real_salary' => $validated['real_salary'],
            ]
        );

        return redirect()
            ->route('simpleWorkflowReport.employee-salaries.index')
            ->with('success', 'حقوق کاربر با موفقیت به‌روزرسانی شد.');
    }

    public static function userMaxAdvances($userId){
        $salary = Employee_salaries::where('user_id', $userId)->first();
        return $salary ? $salary->real_salary - $salary->insurance_salary : 0;
    }
}

<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use BehinFileControl\Controllers\FileController;



class StorePartReport extends Controller
{
    private $case;
    public function __construct()
    {
        
    }

    public function execute(Request $request = null)
    {
        $validated = $request->validate([
            'caseId' => 'required|string|exists:wf_cases,id',
            'fix_report' => 'required',
            'done_date' => 'required|string',
            'done_time' => 'required|regex:/^\d{2}:\d{2}$/', // فرمت ساعت مثل 14:30
            'repair_duration' => 'required|numeric|min:0',
            'see_the_problem' => 'nullable|string',
        ], [
            'caseId.required' => 'شناسه پرونده الزامی است',
            'caseId.integer' => 'شناسه پرونده معتبر نیست',
            'caseId.exists' => 'پرونده مورد نظر یافت نشد',
            'fix_report.required' => 'گزارش تعمیر الزامی است',
            'done_date.required' => 'تاریخ انجام تعمیر الزامی است',
            'done_date.date' => 'تاریخ انجام تعمیر معتبر نیست',
            'done_time.required' => 'ساعت انجام تعمیر الزامی است',
            'done_time.regex' => 'فرمت ساعت صحیح نیست (مثلاً 14:30)',
            'repair_duration.required' => 'مدت زمان تعمیر الزامی است',
            'repair_duration.numeric' => 'مدت زمان تعمیر باید عدد انگلیسی باشد',
        ]);
        $caseId = $request->caseId;
        $case = $this->case = CaseController::getById($caseId);
        $partId = $case->getVariable('part_id');
        $partReport = Part_reports::where('case_id', $request->caseId)
            ->where('case_number', $case->number)
            ->where('fix_report', $request->fix_report)
            ->where('part_id', $partId)
            ->first();
        if($partReport){
            return "گزارش تکراریست";
        }
        
        $doneAt = ConvertDateAndTimeToTimestamp::execute( $request->done_date, $request->done_time );
        
        
        $partReport = new Part_reports();
        $partReport->case_id = $case->id;
        $partReport->case_number = $case->number;
        $partReport->part_id = $partId;
        $partReport->fix_report = $request->fix_report;
        $partReport->done_at = $doneAt;
        $partReport->repair_duration = $request->repair_duration;
        $partReport->see_the_problem = $request->see_the_problem ?? null;
        $partReport->registered_by = Auth::id(); // کاربر جاری
        $partReport->other_parts = $request->other_parts;
        $partReport->special_parts = $request->special_parts;
        $partReport->power = $request->power;
        $partReport->inbox_id = $request->inboxId;
    
        $partReport->save();
        
        return "گزارش ذخیره شد";
    }
}
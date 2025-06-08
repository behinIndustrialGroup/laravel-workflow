<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController as CoreProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflowReport\Controllers\Scripts\TimeoffExport;
use Behin\SimpleWorkflowReport\Controllers\Scripts\TimeoffExport2;
use Maatwebsite\Excel\Facades\Excel;
use Behin\SimpleWorkflow\Models\Entities\Mapa_center_fix_report;
use Behin\SimpleWorkflow\Models\Entities\Mapa_center_install_parts;
use Behin\SimpleWorkflow\Models\Entities\Parts;

class MapaCenterController extends Controller
{
    public function index()
    {
        return redirect()->route('simpleWorkflowReport.summary-report.show', 'ab17ef68-6ec7-4dc8-83b0-5fb6ffcedc50');
    }

    public function show($mapa_center)
    {
        $case = CaseController::getById($mapa_center);
        $reports = Mapa_center_fix_report::where('case_number', $case->number)->get();
        $parts = Parts::where('case_number', $case->number)->get();
        $financials = Financials::where('case_number', $case->number)->get();
        $installParts = Mapa_center_install_parts::where('case_number', $case->number)->get();
        return view('SimpleWorkflowReportView::Core.MapaCenter.show', compact('case', 'reports', 'parts', 'financials', 'installParts'));
    }

    public function updateCaseInfo(Request $request, $mapa_center){
        $data = $request->except('_token', '_method');
        $case = CaseController::getById($mapa_center);
        foreach($data as $key => $value){
            $case->saveVariable($key, $value);
        }
        return redirect()->route('simpleWorkflowReport.mapa-center.show', $mapa_center)->with('success', trans('fields.Case info updated successfully'));
    }

    public function update(Request $request, $mapa_center)
    {
        $case = CaseController::getById($mapa_center);
        // $case->variables()->sync($request->variables); 
        $startTime = convertPersianToEnglish($request->fix_start_time); // مثلاً "۰۸:۱۵"
        $endTime = convertPersianToEnglish($request->fix_end_time);     // مثلاً "۱۴:۴۵"

        $today = Carbon::today()->format('Y-m-d'); // مثلاً "2025-05-01"

        try {
            $start = Carbon::createFromFormat('Y-m-d H:i', "$today $startTime")->timestamp;
            $end = Carbon::createFromFormat('Y-m-d H:i', "$today $endTime")->timestamp;
        } catch (\Exception $e) {
            dd('Invalid time format', $e->getMessage());
        }

        $mapa_center_fix_report = new Mapa_center_fix_report();
        $mapa_center_fix_report->case_id = $mapa_center;
        $mapa_center_fix_report->case_number = $case->number;
        $mapa_center_fix_report->start = $start;
        $mapa_center_fix_report->end = $end;
        $mapa_center_fix_report->expert = Auth::id();
        // $mapa_center_fix_report->unit = $request->refer_to_unit;
        $mapa_center_fix_report->report = $request->fix_report;

        $mapa_center_fix_report->save();
        return redirect()->route('simpleWorkflowReport.mapa-center.show', $mapa_center)->with('success', trans('fields.Report saved successfully'));
    }

    public function excludeDevice(Request $request, $mapa_center)
    {
        $request->validate([
            'part_name' => 'required|string'
        ]);
        $case = CaseController::getById($mapa_center);
        $inbox = ProcessController::startFromScript(
            "9f6b7b5c-155e-4698-8b05-26ebb061bb7d",
            42,
            $case->number,
            $case->id
        );
        $inbox->case_name = "خارج شده از مپاسنتر";
        $inbox->save();
        $newCase = $inbox->case;
        $newCase->copyVariableFrom($mapa_center);
        $newCase->saveVariable('part_name', $request->part_name);
        $newCase->saveVariable('from_mapa_center', 'yes');
        $newCase->saveVariable('initial_description', "این دستگاه توسط: " . Auth::user()->name . "از فرایند مپاسنتر برای تعمیرات داخلی ارسال شده است.");
        return redirect()->route('simpleWorkflowReport.mapa-center.show', $mapa_center)->with('success', trans('دستگاه برای پذیرش داخلی (خانم طالب زاده) جهت انجام مراحل بعدی ارسال شد'));
    }

    public function archive(){
        $process= ProcessController::getById("ab17ef68-6ec7-4dc8-83b0-5fb6ffcedc50");
        return view('SimpleWorkflowReportView::Core.MapaCenter.archive', compact('process'));
    }

    public function installPart(Request $request, $mapa_center){
        $request->validate([
            'part_name' => 'required|string',
            'part_value' => 'required|string',
        ]);
        $case = CaseController::getById($mapa_center);
        $installPart = new Mapa_center_install_parts();
        $installPart->case_id = $mapa_center;
        $installPart->case_number = $case->number;
        $installPart->name = $request->part_name;
        $installPart->value = $request->part_value;
        $installPart->save();
        return redirect()->route('simpleWorkflowReport.mapa-center.show', $mapa_center)->with('success', trans('قطعه روی دستگاه نصب شد'));
    }
}

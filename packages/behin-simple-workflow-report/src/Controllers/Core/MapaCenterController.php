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

use Behin\SimpleWorkflowReport\Controllers\Scripts\TimeoffExport;
use Behin\SimpleWorkflowReport\Controllers\Scripts\TimeoffExport2;
use Maatwebsite\Excel\Facades\Excel;
use Behin\SimpleWorkflow\Models\Entities\Mapa_center_fix_report;


class MapaCenterController extends Controller
{
    public function index()
    {
        return redirect()->route('simpleWorkflowReport.summary-report.show', '5b673183-d0a5-44be-9451-2387fb010109');
    }

    public function show($mapa_center)
    {
        $case = CaseController::getById($mapa_center);
        $reports = Mapa_center_fix_report::where('case_id', $mapa_center)->get();
        return view('SimpleWorkflowReportView::Core.MapaCenter.show', compact('case', 'reports'));
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
        $mapa_center_fix_report->unit = $request->refer_to_unit;
        $mapa_center_fix_report->report = $request->fix_report;

        $mapa_center_fix_report->save();
        return redirect()->route('simpleWorkflowReport.mapa-center.show', $mapa_center)->with('success', trans('fields.Report saved successfully'));
    }
}

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Devices;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflow\Models\Entities\Mapa_center_fix_report;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class ExternalAndInternalReportController extends Controller
{
    public function index(Request $request)
    {
        $cases = Cases::whereIn('process_id', [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de',
            '1763ab09-1b90-4609-af45-ef5b68cf10d0',
        ])
            ->whereNull('parent_id')
            ->whereNotNull('number')
            ->groupBy('number')
            ->get()
            ->filter(function ($case) {
                $whereIsResult = $case->whereIs();
                return !($whereIsResult[0]?->archive == 'yes');
            });
        return view('SimpleWorkflowReportView::Core.ExternalInternal.index', compact('cases'));
    }

    public static function show($caseNumber)
    {
        $mainCase = Cases::where('number', $caseNumber)->whereNull('parent_id')->first();
        $customer = [
            'name' => $mainCase->getVariable('customer_workshop_or_ceo_name'),
            'mobile' => $mainCase->getVariable('customer_mobile'),
            'city' => $mainCase->getVariable('customer_city'),
            'address' => $mainCase->getVariable('customer_address'),
        ];

        $devices = Devices::where('case_number', $caseNumber)->get();
        $deviceRepairReports = Repair_reports::where('case_number', $caseNumber)->get();
        $parts = Parts::where('case_number', $caseNumber)->get();
        if(count($parts) == 0 and $mainCase->getVariable('part_name')){
            Parts::create([
                'case_id' => $mainCase->id,
                'case_number' => $mainCase->number,
                'name' => $mainCase->getVariable('part_name'),
                'serial' => $mainCase->getVariable('part_serial'),
                'mapa_expert_head' => $mainCase->getVariable('mapa_expert_head_for_internal_process'),
                'mapa_expert' => $mainCase->getVariable('mapa_expert'),
                'refer_to_unit' => $mainCase->getVariable('refer_to_unit'),
                'repair_duration' => $mainCase->getVariable('repair_duration'),
                'see_the_problem' => $mainCase->getVariable('see_the_problem'),
                'fix_report' => $mainCase->getVariable('fix_report'),
                'final_result_and_test' => $mainCase->getVariable('final_result_and_test'),
                'test_possibility' => $mainCase->getVariable('test_possibility'),
                'problem_seeing' => $mainCase->getVariable('problem_seeing'),
                'final_result' => $mainCase->getVariable('final_result'),
                'sending_for_test_and_troubleshoot' => $mainCase->getVariable('sending_for_test_and_troubleshoot'),
                'test_in_another_place' => $mainCase->getVariable('test_in_another_place'),
                'job_rank' => $mainCase->getVariable('job_rank'),
                'other_parts' => $mainCase->getVariable('other_parts'),
                'special_parts' => $mainCase->getVariable('special_parts'),
                'power' => $mainCase->getVariable('power'),
                'repair_is_approved' => $mainCase->getVariable('repair_is_approved'),
                'mapa_serial' => $mainCase->getVariable('mapa_serial'),
                'initial_part_pic' => $mainCase->getVariable('initial_device_piv'),
                'has_attachment' => $mainCase->getVariable('has_attachment'),
                'dispatched_expert_needed' => $mainCase->getVariable('dispatched_expert_needed'),
                'dispatched_expert' => $mainCase->getVariable('dispatched_expert'),
                'mapa_expert_companions' => $mainCase->getVariable('mapa_expert_companions'),
                'done_at' => $mainCase->getVariable('done_at'),
                'attachment_image' => $mainCase->getVariable('attachment_image'),
            ]);
            $parts = Parts::where('case_number', $caseNumber)->get();
        }
        $partReports = Part_reports::where('case_number', $caseNumber)->count();
        if($partReports == 0 and $parts->count() > 0){
            foreach ($parts as $part) {
                if($part->fix_report and $part->mapa_expert){
                    Part_reports::create([
                        'part_id' => $part->id,
                        'case_number' => $caseNumber,
                        'case_id' => $mainCase->id,
                        'fix_report' => $part->fix_report,
                        'done_at' => $part->done_at ?? '',
                        'repair_duration' => $part->repair_duration,
                        'see_the_problem' => $part->see_the_problem,
                        'registered_by' => $part->mapa_expert
                    ]);
                }
            }
        }
        $parts = Parts::where('case_number', $caseNumber)->get();
        $mapaCenterReports = Mapa_center_fix_report::where('case_number', $caseNumber)->get();

        $financials = Financials::where('case_number', $caseNumber)->get();
        $delivery = [
            'delivery_date' => $mainCase->getVariable('delivery_date'),
            'delivered_to' => $mainCase->getVariable('delivered_to'),
            'delivery_description' => $mainCase->getVariable('delivery_description'),
        ];
        return view(
            'SimpleWorkflowReportView::Core.ExternalInternal.show',
            compact('mainCase', 'customer', 'devices', 'deviceRepairReports', 'parts', 'financials', 'delivery', 'mapaCenterReports')
        );
    }

    public function search(Request $request)
    {
        if (!$request->actor && !$request->customer && !$request->number && !$request->mapa_serial && !$request->device_name) {
            return [];
        }
        
        $actorCaseNumbers = null;
        $customerCaseNumbers = null;
        $numberCaseNumbers = null;
        $mapaSerialCaseNumbers = null;
        $deviceCaseNumbers = null;
        
        if ($request->actor) {
            $actorCases = Variable::where('key', 'mapa_expert')
                ->where('value', $request->actor)
                ->get();
        
            $actorCaseNumbers = $actorCases
                ->pluck('case.number')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }
        
        if ($request->customer) {
            $customerCases = Variable::where('key', 'customer_workshop_or_ceo_name')
                ->where('value', 'like', "%$request->customer%")
                ->get();
        
            $customerCaseNumbers = $customerCases
                ->pluck('case.number')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }
        
        if ($request->number) {
            $numberCases = Cases::whereIn('process_id', [
                    '35a5c023-5e85-409e-8ba4-a8c00291561c',
                    '4bb6287b-9ddc-4737-9573-72071654b9de',
                    '1763ab09-1b90-4609-af45-ef5b68cf10d0',
                    'ab17ef68-6ec7-4dc8-83b0-5fb6ffcedc50'
                ])
                ->where('number', 'like', "%$request->number%")
                ->pluck('number')
                ->unique()
                ->toArray();
        
            $numberCaseNumbers = $numberCases;
        }

        if ($request->mapa_serial) {
            $mapaSerialCases = Variable::where('key', 'mapa_serial')
                ->where('value', 'like', "%$request->mapa_serial%")
                ->get();

            $mapaSerialCaseNumbers = $mapaSerialCases
                ->pluck('case.number')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }

        if($request->device_name){
            $deviceCases = Devices::where('name', 'like', "%$request->device_name%")->get();
            $deviceCaseNumbers = $deviceCases->pluck('case_number')->unique()->toArray();
        }
        
        // گرفتن اشتراک همه لیست‌ها
        $allLists = array_filter([$actorCaseNumbers, $customerCaseNumbers, $numberCaseNumbers, $mapaSerialCaseNumbers, $deviceCaseNumbers]);
        
        if (count($allLists) === 0) {
            return [];
        }
        
        // گرفتن اشتراک همه لیست‌ها با هم
        $finalCaseNumbers = array_shift($allLists);
        foreach ($allLists as $list) {
            $finalCaseNumbers = array_intersect($finalCaseNumbers, $list);
        }
        
        if (count($finalCaseNumbers) === 0) {
            return []; // هیچ کیس مطابق با همه شرایط پیدا نشد
        }
        
        $cases = Cases::whereIn('number', $finalCaseNumbers)
            ->whereIn('process_id', [
                '35a5c023-5e85-409e-8ba4-a8c00291561c',
                '4bb6287b-9ddc-4737-9573-72071654b9de',
                '1763ab09-1b90-4609-af45-ef5b68cf10d0',
                'ab17ef68-6ec7-4dc8-83b0-5fb6ffcedc50'
            ])
            ->groupBy('number')
            ->get();
        
        $s = '';
        foreach ($cases as $case) {
            $a = "<a href='" . route('simpleWorkflowReport.external-internal.show', ['external_internal' => $case->number]) . "'><i class='fa fa-external-link'></i></a>";
            $s .= "<tr><td>
                    $a
                    $case->number
                    $case->history
            </td>";
            $s .= "<td>" . $case->getVariable('customer_workshop_or_ceo_name') . "</td>";
            $s .= "<td>" . $case->getVariable('device_name') . "</td>";
            $s .= "<td>";
            foreach ($case->whereIs() as $inbox) {
                $s .= $inbox->task->styled_name ?? '';
                $s .= '(' . getUserInfo($inbox->actor)?->name . ')';
                $s .= '<br>';
            }
            $s .= "</td><td dir='ltr'>" . toJalali($case->created_at)->format('Y-m-d H:i') . "</td></tr>";
        }
        return $s;
    }

    public function archive()
    {
        $cases = Cases::whereIn('process_id', [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de',
            '1763ab09-1b90-4609-af45-ef5b68cf10d0',
            'ab17ef68-6ec7-4dc8-83b0-5fb6ffcedc50'
        ])
            ->whereNull('parent_id')
            ->whereNotNull('number')
            ->groupBy('number')
            ->get()
            ->filter(function ($case) {
                $whereIsResult = $case->whereIs();
                return ($whereIsResult[0]?->archive == 'yes');
            });
        return view('SimpleWorkflowReportView::Core.ExternalInternal.archive', compact('cases'));
    }
}

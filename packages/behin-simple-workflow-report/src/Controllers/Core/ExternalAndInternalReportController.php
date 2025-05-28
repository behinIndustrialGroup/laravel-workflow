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

    public function show($caseNumber)
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
        if (!$request->actor && !$request->customer && !$request->number && !$request->mapa_serial) {
            return [];
        }
        
        $actorCaseNumbers = null;
        $customerCaseNumbers = null;
        $numberCaseNumbers = null;
        $mapaSerialCaseNumbers = null;
        
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
        
        // گرفتن اشتراک همه لیست‌ها
        $allLists = array_filter([$actorCaseNumbers, $customerCaseNumbers, $numberCaseNumbers, $mapaSerialCaseNumbers]);
        
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

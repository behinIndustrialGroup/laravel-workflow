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
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Devices;
use Behin\SimpleWorkflow\Models\Entities\Financials;
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
            '1763ab09-1b90-4609-af45-ef5b68cf10d0'
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

    public function show($caseNumber){
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
        $financials = Financials::where('case_number', $caseNumber)->get();
        $delivery = [
            'delivery_date' => $mainCase->getVariable('delivery_date'),
            'delivered_to' => $mainCase->getVariable('delivered_to'),
            'delivery_description' => $mainCase->getVariable('delivery_description'),
        ];
        return view('SimpleWorkflowReportView::Core.ExternalInternal.show',
        compact('mainCase', 'customer', 'devices', 'deviceRepairReports', 'parts', 'financials', 'delivery'));
    }

    public function search(Request $request){
        $cases = Variable::where('key', 'customer_workshop_or_ceo_name')->where('value', 'like', "%$request->q%")->get();
        $caseNumbers = [];
        foreach($cases as $case){
            if(isset($case->case->number)){
                $caseNumbers[] = $case->case->number;
            }
        }
        $cases = Cases::whereIn('number', $caseNumbers)
        ->orWhere('number', 'like', '%' . $request->q . '%')
        ->groupBy('number')
        ->get();
        $s = '';
        foreach($cases as $case){
            $a = "<a href='" . route('simpleWorkflowReport.external-internal.show', [ 'external_internal' => $case->number ]) . "'><i class='fa fa-external-link'></i></a>";
            $s .= "<tr><td>
                    $a
                    $case->number
                    $case->history
            </td>";   
            $s .= "<td>". $case->getVariable('customer_workshop_or_ceo_name') . "</td>";   
            $s .= "<td>";
            foreach ($case->whereIs() as $inbox) {
                $s .= $inbox->task->styled_name ?? '';
                $s .= '(' . getUserInfo($inbox->actor)?->name . ')';
                $s .= '<br>';
            } 
            $s .= "</td><td dir='ltr'>". toJalali($case->created_at)->format('Y-m-d H:i') . "</td></tr>";   
        }
        return $s;
    }

    public function archive(){
        $cases = Cases::whereIn('process_id', [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de'
        ])
        ->whereNull('parent_id')
        ->whereNotNull('number')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('wf_inbox')
                ->whereNull('wf_inbox.deleted_at')
                ->whereColumn('wf_inbox.case_id', 'wf_cases.id')
                ->whereIn('status', ['done', 'doneByOther', 'canceled']);
        })
        ->groupBy('number')
        ->get();
        return view('SimpleWorkflowReportView::Core.ExternalInternal.archive', compact('cases'));
    }
}

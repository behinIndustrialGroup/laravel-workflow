<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;
use Behin\SimpleWorkflow\Models\Entities\Borrow_requests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Consumable_parts;
use Illuminate\Support\Facades\Log;

class ConsumablePartController extends Controller
{
    public function index()
    {
        $consumableParts = Consumable_parts::orderBy('created_at', 'desc')->get();
        $statuses = config('simpleWorkflowReport.consumable_parts.statuses', []);
        return view('SimpleWorkflowReportView::Core.ConsumablePart.index', compact('consumableParts', 'statuses'));
    }

    public function buyingList()
    {
        $consumableParts = Consumable_parts::all();
        $statuses = config('simpleWorkflowReport.consumable_parts.statuses', []);
        return view('SimpleWorkflowReportView::Core.ConsumablePart.buying-list', compact('consumableParts', 'statuses'));
    }

    public function create()
    {
        $userConsumableParts = Consumable_parts::where('created_by', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('SimpleWorkflowReportView::Core.ConsumablePart.create', compact('userConsumableParts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required',
            'requested_quantity' => 'required',
        ]);
        $validated['created_by'] = Auth::id();
        $row = Consumable_parts::create($validated);
        $request->merge(['rowId' => $row->id]);
        ScriptController::runFromView($request, "3aa39ac6-759b-4be3-b7b8-2afcf4d33e68");
        return redirect()->back()->with(['success' => 'ثبت شد']);
    }

    public function deliver(Request $request, Consumable_parts $consumablePart)
    {

        $consumablePart->consumable_part_status = $request->consumable_part_status;
        $consumablePart->delivered_quantity = $request->delivered_quantity;
        // if($request->consumable_part_status == 'تایید' and !$consumablePart->delivered_quantity){
        //     return redirect()->back()->with(['error' => 'تعداد تحویل داده شده الزامیست']);
        // }

        if (isset($request->required_quantity_to_purchase)) {
            $consumablePart->required_quantity_to_purchase = $request->required_quantity_to_purchase;
        }
        if (isset($request->purchased_quantity)) {
            $consumablePart->purchased_quantity = $request->purchased_quantity;
        }
        $consumablePart->save();
        return redirect()->back()->with(['success' => 'ذخیره شد']);
    }
}

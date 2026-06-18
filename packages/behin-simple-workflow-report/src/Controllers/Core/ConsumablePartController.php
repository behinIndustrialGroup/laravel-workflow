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
use Behin\Sms\Controllers\ArsanSmsController;
use Brick\Math\BigInteger;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        if ($consumablePart->consumable_part_status == 'در انتظار تحویل انبار') {
            if ($request->consumable_part_status == 'تایید' and !$request->delivered_quantity) {
                return redirect()->back()->with(['error' => 'تعداد تحویل داده شده الزامیست']);
            }
            if (isset($request->required_quantity_to_purchase)) {
                $consumablePart->required_quantity_to_purchase = $request->required_quantity_to_purchase;
            }
            $consumablePart->consumable_part_status = $request->consumable_part_status;
            $consumablePart->delivered_quantity = $request->delivered_quantity;
            $consumablePart->amount = $request->amount;
            $consumablePart->supply_source = $request->supply_source;
            $consumablePart->invoice_number = $request->invoice_number;
            $consumablePart->invoice_date = $request->invoice_date;
            $consumablePart->invoice_date_alt = $request->invoice_date_alt;
            $consumablePart->save();
            return redirect()->back()->with(['success' => 'ذخیره شد']);
        } elseif ($consumablePart->consumable_part_status == 'در انتظار تایید بازگشتی ها توسط انبار') {
            $consumablePart->consumable_part_status = $request->consumable_part_status;
            $consumablePart->delivered_quantity = $request->delivered_quantity;
            $consumablePart->amount = $request->amount;
            $consumablePart->supply_source = $request->supply_source;
            $consumablePart->invoice_number = $request->invoice_number;
            $consumablePart->invoice_date = $request->invoice_date;
            $consumablePart->invoice_date_alt = $request->invoice_date_alt;
            $consumablePart->save();
            return redirect()->back()->with(['success' => 'ذخیره شد']);
        } else {
            $consumablePart->consumable_part_status = $request->consumable_part_status;
            if (isset($request->required_quantity_to_purchase)) {
                $consumablePart->required_quantity_to_purchase = $request->required_quantity_to_purchase;
            }
            if (isset($request->purchased_quantity)) {
                $consumablePart->purchased_quantity = $request->purchased_quantity;
            }
            $consumablePart->amount = $request->amount;
            $consumablePart->supply_source = $request->supply_source;
            $consumablePart->invoice_number = $request->invoice_number;
            $consumablePart->invoice_date = $request->invoice_date;
            $consumablePart->invoice_date_alt = $request->invoice_date_alt;
            $consumablePart->save();
            return redirect()->back()->with(['success' => 'ذخیره شد']);
        }
    }

    public function storeExternalBuyingInfo(Request $request, Consumable_parts $consumablePart)
    {
        try {
            $consumablePart->consumable_part_status = $request->consumable_part_status;
            if (isset($request->purchased_quantity)) {
                $consumablePart->purchased_quantity = $request->purchased_quantity;
                $consumablePart->external_buying_is_done = 1;
            }
            $consumablePart->amount = str_replace(',', '',$request->amount, );
            $consumablePart->supply_source = $request->supply_source;
            $consumablePart->invoice_number = $request->invoice_number;
            $consumablePart->invoice_date = $request->invoice_date;
            $consumablePart->invoice_date_alt = $request->invoice_date_alt;
            $consumablePart->save();
            return redirect()->back()->with(['success' => 'ذخیره شد']);
        } catch (Throwable $th) {
            return redirect()->back()->with(['error' => $th->getMessage()]);
        }
    }

    public function report()
    {
        $fromDate = request('from_date') ?? null;
        $toDate = request('to_date') ?? null;
        $dateColumn = request('date_column') ?? 'created_at';
        $perPage = request('per_page') ?? 25;
        $item_name = request('item_name') ?? null;
        $requesterId = request('requester_id') ?? null;
        $status = request('status') ?? null;
        $users = User::all();
        $statuses = config('simpleWorkflowReport.consumable_parts.statuses', []);

        $allRequests = Consumable_parts::query();
        if ($fromDate) {
            $fromDate .= ' 00:00';
            $fromDateAlt = convertPersianDateTimeToTimestamp($fromDate);
            $fromDateAlt = date('Y-m-d H:i:s', $fromDateAlt);
            $allRequests->where($dateColumn, '>=', $fromDateAlt);
        }
        if ($toDate) {
            $toDate .= ' 23:59';
            $toDateAlt = convertPersianDateTimeToTimestamp($toDate);
            $toDateAlt = date('Y-m-d H:i:s', $toDateAlt);
            $allRequests->where($dateColumn, '<=', $toDateAlt);
        }
        if ($requesterId) {
            $allRequests->where('created_by', $requesterId);
        }
        if ($item_name) {
            $allRequests->where('product_name', 'like', '%' . $item_name . '%');
        }
        if ($status) {
            if ($status == 'تایید') {
                $allRequests->where('consumable_part_status',  $status);
            } else {
                $allRequests->whereNot('consumable_part_status', 'تایید')->where('consumable_part_status',  $status);
            }
        } else {
            $allRequests->whereNot('consumable_part_status', 'تایید');
        }
        $allRequests = $allRequests->orderByDesc($dateColumn)->paginate($perPage)->withQueryString();

        return view('SimpleWorkflowReportView::Core.ConsumablePart.report', compact('allRequests', 'users', 'statuses'));
    }

    public function destroy(Consumable_parts $consumablePart)
    {
        $consumablePart->delete();
        return redirect()->back()->with(['success' => 'حذف شد']);
    }
}

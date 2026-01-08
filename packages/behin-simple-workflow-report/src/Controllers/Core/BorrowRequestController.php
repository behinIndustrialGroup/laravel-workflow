<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Borrow_requests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class BorrowRequestController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $myRequests = Borrow_requests::where('requester_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $pendingDeliveries = Borrow_requests::whereStatus('requested')
            ->orderByDesc('created_at')
            ->get();

        $deliveredRequests = Borrow_requests::whereStatus('delivered')->whereNotNull('delivered_at')
            ->whereNull('actual_return_date')
            ->orderByDesc('delivered_at')
            ->get();

        $waitingReturnConfirmation = Borrow_requests::whereStatus('pending_return_confirmation')
            ->orderByDesc('actual_return_date')
            ->get()
            ->filter(fn ($request) => $request->status === 'pending_return_confirmation');

        $statuses = config('simpleWorkflowReport.borrow_requests.statuses', []);

        return view('SimpleWorkflowReportView::Core.BorrowRequest.index', compact(
            'myRequests',
            'pendingDeliveries',
            'deliveredRequests',
            'waitingReturnConfirmation',
            'statuses'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_name' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'item_id' => 'nullable|string',
            'case_id' => 'nullable|integer',
            'case_number' => 'nullable|string',
        ]);

        $data['requester_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['status'] = config('simpleWorkflowReport.borrow_requests.statuses.requested.key');

        Borrow_requests::create($data);

        return redirect()->back()->with('success', 'درخواست شما ثبت شد.');
    }

    public function deliver(Request $request, Borrow_requests $borrowRequest): RedirectResponse
    {
        $validated = $request->validate([
            'delivered_at' => 'required|string',
            'expected_return_date' => 'nullable|string',
        ]);

        if (!is_null($borrowRequest->actual_return_date)) {
            return redirect()->back()->with('warning', 'برای درخواست تحویل‌گرفته‌شده امکان ویرایش اطلاعات تحویل وجود ندارد.');
        }

        $deliveredAtAlt = convertPersianDateTimeToTimestamp($request->delivered_at);
        $expectedReturnJalali = null;
        if (!empty($validated['expected_return_date'])) {
            $expected = convertPersianDateTimeToTimestamp($validated['expected_return_date']);
        }

        $borrowRequest->update([
            'delivery_id' => Auth::id(),
            'delivered_at_alt' => $deliveredAtAlt,
            'delivered_at' => $validated['delivered_at'],
            'expected_return_date_alt' => $expected,
            'expected_return_date' => $validated['expected_return_date'] ?? null,
            'status' => config('simpleWorkflowReport.borrow_requests.statuses.delivered.key'),
            'updated_by' => Auth::id(),
        ]);

        $message = is_null($borrowRequest->getOriginal('delivered_at'))
            ? 'درخواست تحویل شد و تاریخ‌ها ثبت گردید.'
            : 'اطلاعات تحویل به‌روزرسانی شد.';

        return redirect()->back()->with('success', $message);
    }

    public function markReturned(Borrow_requests $borrowRequest): RedirectResponse
    {
        if ($borrowRequest->status !== 'delivered') {
            return redirect()->back()->with('warning', 'این درخواست در وضعیت تحویل به درخواست‌کننده نیست.');
        }

        if ($borrowRequest->requester_id != Auth::id()) {
            return redirect()->back()->with('warning', 'تنها درخواست‌کننده می‌تواند تحویل کالا را ثبت کند.');
        }

        $now = Jalalian::now();

        $borrowRequest->update([
            'actual_return_date' => $now->format('Y-m-d'),
            'actual_return_date_alt' => $now->toCarbon()->timestamp,
            'updated_by' => Auth::id(),
            'status' => config('simpleWorkflowReport.borrow_requests.statuses.pending_return_confirmation.key')
        ]);

        return redirect()->back()->with('success', 'بازگشت کالا ثبت شد و در انتظار تایید انبار است.');
    }

    public function confirmReturn(Borrow_requests $borrowRequest): RedirectResponse
    {
        if ($borrowRequest->status !== 'pending_return_confirmation') {
            return redirect()->back()->with('warning', 'درخواست در انتظار تایید بازگشت نیست.');
        }

        $now = Jalalian::now();

        $borrowRequest->setReturnConfirmation([
            'by' => Auth::id(),
            'at' => $now->format('Y-m-d'),
            'at_alt' => $now->toCarbon()->timestamp,
        ]);

        $borrowRequest->status = config('simpleWorkflowReport.borrow_requests.statuses.returned.key');
        $borrowRequest->updated_by = Auth::id();
        $borrowRequest->save();

        return redirect()->back()->with('success', 'بازگشت کالا تایید شد.');
    }

    private function normalizeDate(string $value): string
    {
        return str_replace('/', '-', convertPersianToEnglish(trim($value)));
    }
}

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflowReport\Controllers\Scripts\PettyCashExport;
use Behin\SimpleWorkflowReport\Models\PettyCash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;

class PettyCashController extends Controller
{
    public function index(Request $request)
    {
        $query = PettyCash::query();

        $monthOptions = [];
        $currentMonth = Jalalian::now();
        for ($i = 0; $i < 12; $i++) {
            $month = clone $currentMonth;
            if ($i > 0) {
                $month = $month->subMonths($i);
            }

            $monthOptions[] = [
                'value' => $month->format('Y-m'),
                'label' => $month->format('%B %Y'),
                'from' => $month->getFirstDayOfMonth()->format('Y-m-d'),
                'to' => $month->getEndDayOfMonth()->format('Y-m-d'),
            ];
        }

        $selectedMonthInput = $request->input('month', $monthOptions[0]['value']);
        $selectedMonth = convertPersianToEnglish($selectedMonthInput);
        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = $monthOptions[0]['value'];
        } else {
            [, $selectedMonthNumber] = array_map('intval', explode('-', $selectedMonth));
            if ($selectedMonthNumber < 1 || $selectedMonthNumber > 12) {
                $selectedMonth = $monthOptions[0]['value'];
            }
        }

        $selectedMonthJalali = Jalalian::fromFormat('Y-m-d', $selectedMonth . '-01');
        $defaultFrom = $selectedMonthJalali->getFirstDayOfMonth()->format('Y-m-d');
        $defaultTo = $selectedMonthJalali->getEndDayOfMonth()->format('Y-m-d');

        $fromValue = $request->filled('from') ? convertPersianToEnglish($request->input('from')) : $defaultFrom;
        $toValue = $request->filled('to') ? convertPersianToEnglish($request->input('to')) : $defaultTo;

        if ($fromValue) {
            $from = convertPersianToEnglish($fromValue);
            $from = Jalalian::fromFormat('Y-m-d', $from)
                ->toCarbon()
                ->startOfDay()
                ->timestamp;
            $query->where('paid_at', '>=', $from);
        }
        if ($toValue) {
            $to = convertPersianToEnglish($toValue);
            $to = Jalalian::fromFormat('Y-m-d', $to)
                ->toCarbon()
                ->endOfDay()
                ->timestamp;
            $query->where('paid_at', '<=', $to);
        }
        $pettyCashes = $query->orderByDesc('paid_at')->get();
        return view('SimpleWorkflowReportView::Core.PettyCash.index', compact(
            'pettyCashes',
            'monthOptions',
            'selectedMonth',
            'fromValue',
            'toValue'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'paid_at' => 'required|string',
            'from_account' => 'nullable|string',
        ]);
        $data['paid_at'] = convertPersianToEnglish($data['paid_at']);
        $data['paid_at'] = Jalalian::fromFormat('Y-m-d', $data['paid_at'])
            ->toCarbon()
            ->setTimezone('Asia/Tehran')
            ->setTime(12, 0, 0, 0)
            ->timestamp;

        PettyCash::create($data);
        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }

    public function edit(PettyCash $pettyCash)
    {
        return view('SimpleWorkflowReportView::Core.PettyCash.edit', compact('pettyCash'));
    }

    public function update(Request $request, PettyCash $pettyCash)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'paid_at' => 'required|string',
            'from_account' => 'nullable|string',
        ]);
        $data['paid_at'] = convertPersianToEnglish($data['paid_at']);
        $data['paid_at'] = Jalalian::fromFormat('Y-m-d', $data['paid_at'])
            ->toCarbon()
            ->setTimezone('Asia/Tehran')
            ->setTime(12, 0, 0, 0)
            ->timestamp;
        $pettyCash->update($data);
        return redirect()->route('simpleWorkflowReport.petty-cash.index')->with('success', 'با موفقیت ذخیره شد.');
    }

    public function destroy(PettyCash $pettyCash)
    {
        $pettyCash->delete();
        return redirect()->back()->with('success', 'با موفقیت حذف شد.');
    }

    public function export(Request $request)
    {
        $monthInput = $request->input('month', Jalalian::now()->format('Y-m'));
        $month = convertPersianToEnglish($monthInput);
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = Jalalian::now()->format('Y-m');
        } else {
            [, $monthNumber] = array_map('intval', explode('-', $month));
            if ($monthNumber < 1 || $monthNumber > 12) {
                $month = Jalalian::now()->format('Y-m');
            }
        }

        $monthJalali = Jalalian::fromFormat('Y-m-d', $month . '-01');
        $defaultFrom = $monthJalali->getFirstDayOfMonth()->format('Y-m-d');
        $defaultTo = $monthJalali->getEndDayOfMonth()->format('Y-m-d');

        $from = $request->input('from', $defaultFrom);
        $to = $request->input('to', $defaultTo);

        return Excel::download(new PettyCashExport($from, $to), 'petty_cash.xlsx');
    }
}


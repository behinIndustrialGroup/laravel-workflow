<?php

namespace Arghavan\PettyCash\Controllers;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflowReport\Models\PettyCash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Petty_cash_transactions;

class StorePettyCashController extends Controller
{
    public static function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'paid_at' => 'required|string',
            'from_account' => 'nullable|string',
            'case_number' => 'nullable|string',
        ]);
        $data['paid_at'] = convertPersianToEnglish($data['paid_at']);
        $data['paid_at_alt'] = Jalalian::fromFormat('Y-m-d', $data['paid_at'])
            ->toCarbon()
            ->setTimezone('Asia/Tehran')
            ->setTime(12, 0, 0, 0)
            ->timestamp;

        Petty_cash_transactions::create($data);
        return redirect()->back()->with('success', 'با موفقیت ذخیره شد.');
    }
}


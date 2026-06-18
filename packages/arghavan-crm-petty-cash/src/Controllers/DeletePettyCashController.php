<?php

namespace Arghavan\PettyCash\Controllers;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflowReport\Models\PettyCash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Petty_cash_transactions;

class DeletePettyCashController extends Controller
{
    public static function delete(Financial_transactions $financialTransaction)
    {
        $financialTransaction->delete();
        return redirect()->back()->with('success', 'با موفقیت حذف شد.');
    }
}


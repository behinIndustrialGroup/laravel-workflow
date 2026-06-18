<?php

namespace Arghavan\PettyCash\Controllers;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Financial_transactions;
use Behin\SimpleWorkflowReport\Models\PettyCash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Petty_cash_transactions;
class GetPettyCashController extends Controller
{
    public static function baseQuery()
    {
        $query = Financial_transactions::query();
        $query = $query->where('counterparty_id', '9PqviRDYsu');
        return $query;
    }
}


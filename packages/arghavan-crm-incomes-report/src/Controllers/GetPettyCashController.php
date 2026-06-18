<?php

namespace Arghavan\PettyCash\Controllers;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflowReport\Models\PettyCash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities\Petty_cash_transactions;
class GetPettyCashController extends Controller
{
    public static function baseQuery()
    {
        $query = Petty_cash_transactions::query();

        return $query;
    }
}


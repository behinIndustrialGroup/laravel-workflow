<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Customers;

class PhonebookController extends Controller
{
    public function index()
    {
        $customers = Customers::all();

        return view('SimpleWorkflowReportView::Core.Phonebook.index', compact('customers'));
    }
}


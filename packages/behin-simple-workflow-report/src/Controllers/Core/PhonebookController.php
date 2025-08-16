<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Customers;
use Illuminate\Http\Request;


class PhonebookController extends Controller
{
    public function index()
    {
        $customers = Customers::all();

        return view('SimpleWorkflowReportView::Core.Phonebook.index', compact('customers'));
    }

    public function create()
    {
        return view('SimpleWorkflowReportView::Core.Phonebook.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required',
            'mobile' => 'required',
            'national_id' => 'nullable',
            'address' => 'nullable',
        ]);

        Customers::create($data);

        return redirect()->route('simpleWorkflowReport.phonebook.index');
    }

    public function edit(Customers $phonebook)
    {
        return view('SimpleWorkflowReportView::Core.Phonebook.edit', ['customer' => $phonebook]);
    }

    public function update(Request $request, Customers $phonebook)
    {
        $data = $request->validate([
            'fullname' => 'required',
            'mobile' => 'required',
            'national_id' => 'nullable',
            'address' => 'nullable',
        ]);

        $phonebook->update($data);

        return redirect()->route('simpleWorkflowReport.phonebook.index');
    }

    public function destroy(Customers $phonebook)
    {
        $phonebook->delete();

        return redirect()->route('simpleWorkflowReport.phonebook.index');
    }
}


<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Customers;
use Illuminate\Http\Request;


class PhonebookController extends Controller
{
    public function index(Request $request)
    {
        $query = Customers::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', "%" . $request->name . "%");
        }

        if ($request->filled('mobile')) {
            $query->where('mobile', 'like', "%" . $request->mobile . "%");
        }

        // اگر هیچ فیلتری وارد نشده و درخواست نمایش همه نیز وجود نداشته باشد، نتیجه خالی است
        if (! $request->filled('name') && ! $request->filled('mobile') && ! $request->boolean('show_all')) {
            $customers = collect(); // یک کالکشن خالی
        } else {
            $customers = $query->get();
        }

        return view('SimpleWorkflowReportView::Core.Phonebook.index', compact('customers'));
    }


    public function create()
    {
        return view('SimpleWorkflowReportView::Core.Phonebook.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
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
            'name' => 'required',
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
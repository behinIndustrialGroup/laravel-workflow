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
use Behin\SimpleWorkflow\Models\Entities\Parts;
use Illuminate\Support\Facades\Log;

class PartController extends Controller
{
    public function edit(Parts $part)
    {
        return view('SimpleWorkflowReportView::Core.Part.edit', compact('part'));
    }

    public function update(Request $request, Parts $part)
    {
        $validate = $request->validate([
            'name' => ['required'],
            'mapa_serial' => ['required']
        ]);
        $part->update($validate);
        return redirect()->back()->with(['success' => 'ویرایش شد']);
    }

}

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Behin\SimpleWorkflowReport\Helper\ReportHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class PersonelActivityController extends Controller
{
    private $allowedProcessIds;
    public function __construct()
    {
        $this->allowedProcessIds = [
            '35a5c023-5e85-409e-8ba4-a8c00291561c',
            '4bb6287b-9ddc-4737-9573-72071654b9de',
            'ee209b0a-251c-438e-ab14-2018335eba6d'
        ];
    }
    public function index(Request $request)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        // تاریخ‌ها را به میلادی تبدیل کن اگر شمسی هستند (اینجا فرض می‌کنیم شمسی هستند)
        $from = convertPersianToEnglish($request->from_date);
        $to = convertPersianToEnglish($request->to_date);
        $from = $request->from_date ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $request->to_date ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $query = User::query();
        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get()->each(function ($row) use ($allowedProcessIds, $from, $to) {
            $row->inbox = Inbox::where('actor', $row->id)
                ->where('status', 'new')
                ->count();

            $doneQuery = Inbox::where('actor', $row->id)
                ->where('status', 'done')
                ->whereHas('task.process', function ($query) use ($allowedProcessIds) {
                    $query->whereIn('id', $allowedProcessIds);
                })
                ->with('case');
            if ($from) {
                $doneQuery->whereDate('updated_at', '>=', $from);
            }

            if ($to) {
                $doneQuery->whereDate('updated_at', '<=', $to);
            }
            $row->done = $doneQuery // جلوگیری از n+1 اگر بعداً در ویو استفاده شد
                ->get()
                ->unique(function ($item) {
                    return $item->case?->number; // یونیک بر اساس نام پرونده
                })
                ->count(); // مستقیماً شمارش بدون ذخیره مجموعه

        });

        return view('SimpleWorkflowReportView::Core.PersonelActivity.index', compact('users'));
    }

    public function showDones($user_id, $from = null, $to = null)
    {
        $allowedProcessIds = $this->allowedProcessIds;
        $from = $from ? convertPersianToEnglish($from) : null;
        $to = $to ? convertPersianToEnglish($to) : null;
        $from = $from ? Jalalian::fromFormat('Y-m-d', $from)->toCarbon() : null;
        $to = $to ? Jalalian::fromFormat('Y-m-d', $to)->toCarbon()->endOfDay() : null;

        $doneQuery = Inbox::where('actor', $user_id)
            ->where('status', 'done')
            ->whereHas('task.process', function ($query) use ($allowedProcessIds) {
                $query->whereIn('id', $allowedProcessIds);
            })
            ->with('case');
        if ($from) {
            $doneQuery->whereDate('updated_at', '>=', $from);
        }

        if ($to) {
            $doneQuery->whereDate('updated_at', '<=', $to);
        }
        $items = $doneQuery
            ->get()
            ->unique(function ($item) {
                return $item->case?->number;
            });

        return view('SimpleWorkflowReportView::Core.PersonelActivity.show', compact('items'));
    }
}

<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;


class TimeoffController extends Controller
{
    public function update(Request $request)
    {
        $year = Jalalian::now()->format('%Y');
        // DB::table('wf_entity_timeoffs')->where('user', $request->userId)->where('request_year', $year)->update(['deleted_at' => Carbon::now()]);;
        $duration = $request->restBySystem - $request->restByUser;
        Timeoffs::create(
            [
                'user' => $request->userId,
                'type' => 'ساعتی',
                'duration' => $duration,
                'approved' => 1,
                'request_year' => $year,
                'start_year' => $year,
                'request_timestamp' => time(),
                'start_timestamp' => time(),
                'request_month' => Jalalian::now()->format('%m'),
                'start_month' => Jalalian::now()->format('%m'),
                'uniqueId' => 'به صورت دستی'
            ]
        );
        return redirect()->back();
    }

    public static function totalLeaves($userId = null)
    {
        $todayShamsi = Jalalian::now();

        $thisYear = $todayShamsi->getYear();
        $thisMonth = str_pad($todayShamsi->getMonth(), 2, '0', STR_PAD_LEFT);
        $startOfThisJalaliYear = Jalalian::fromFormat('Y-m-d', $thisYear . '-01-01')->toCarbon()->timestamp;
        $users = DB::table('users')->whereNotIn('id', [1, 43]);
        if ($userId) {
            $users = $users->where('id', $userId)->orderBy('number')->get();
        } else {
            $users = $users->orderBy('number')->get();
        }
        foreach ($users as $user) {
            $approvedLeaves = Timeoffs::select(
                DB::raw(
                    'COALESCE(SUM(CASE WHEN wf_entity_timeoffs.type = "ساعتی" THEN duration ELSE duration*8 END), 0) as total_leaves',
                ),
            )
                ->where('user', $user->id)
                ->where('start_timestamp', '>', $startOfThisJalaliYear)
                ->where('approved', 1)
                ->first()->total_leaves;
            $user->approvedLeaves = $approvedLeaves;
            $restLeaves = $thisMonth * 20 - $approvedLeaves;
            $user->restLeaves = $restLeaves;
        }
        return $users;
    }

    public static function items($userId)
    {
        $todayShamsi = Jalalian::now();
        $thisYear = $todayShamsi->getYear();
        $thisMonth = str_pad($todayShamsi->getMonth(), 2, '0', STR_PAD_LEFT);
        $startOfToday = Carbon::today()->timestamp;
        $thisYearTimestamp = Carbon::create($thisYear, 1, 1)->timestamp;
        $thisMonthTimestamp = Carbon::create($thisYear, $thisMonth, 1)->timestamp;
        if ($userId) {
            $items = Timeoffs::whereNot('uniqueId', 'به صورت دستی')
                ->where('start_timestamp', '>=', $thisYearTimestamp)
                ->where('approved', 1)
                ->where('user', $userId)
                ->orderBy('start_timestamp', 'desc')
                ->get();
        } else {
            $items = Timeoffs::whereNot('uniqueId', 'به صورت دستی')->where('start_timestamp', '>=', $startOfToday)->where('approved', 1)->orderBy('start_timestamp', 'desc')->get();
        }
        return $items;
    }
}

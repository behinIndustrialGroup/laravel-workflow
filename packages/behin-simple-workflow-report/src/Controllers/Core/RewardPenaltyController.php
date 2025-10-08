<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflowReport\Models\RewardPenalty;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;
use Throwable;

class RewardPenaltyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RewardPenalty::query()
            ->with(['user:id,name,number']);

        if ($request->filled('type')) {
            $type = $request->input('type');
            if (in_array($type, [RewardPenalty::TYPE_REWARD, RewardPenalty::TYPE_PENALTY], true)) {
                $query->where('type', $type);
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($from = $this->parseJalaliDate($request->input('from_date'))) {
            $query->where('created_at', '>=', $from->copy()->startOfDay());
        }

        if ($to = $this->parseJalaliDate($request->input('to_date'))) {
            $query->where('created_at', '<=', $to->copy()->endOfDay());
        }

        $records = $query
            ->orderBy('created_at')
            ->get()
            ->map(function (RewardPenalty $rewardPenalty) {
                return [
                    'id' => $rewardPenalty->id,
                    'user_id' => $rewardPenalty->user_id,
                    'user_name' => $rewardPenalty->user?->name,
                    'user_number' => $rewardPenalty->user?->number,
                    'type' => $rewardPenalty->type,
                    'description' => $rewardPenalty->description,
                    'amount' => $rewardPenalty->amount,
                    'formatted_amount' => number_format($rewardPenalty->amount),
                    'recorded_at' => Jalalian::fromCarbon($rewardPenalty->created_at)->format('Y-m-d'),
                    'created_at' => $rewardPenalty->created_at,
                    'updated_at' => $rewardPenalty->updated_at,
                ];
            })
            ->values();

        return response()->json([
            'data' => $records,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::in([RewardPenalty::TYPE_REWARD, RewardPenalty::TYPE_PENALTY])],
            'description' => ['required', 'string', 'max:65535'],
            'amount' => ['required', 'min:0'],
            'recorded_at' => ['nullable', 'string'],
        ]);

        $rewardPenalty = new RewardPenalty(Arr::except($validated, ['recorded_at']));

        if (! empty($validated['recorded_at'])) {
            if ($recordedAt = $this->parseJalaliDate($validated['recorded_at'])) {
                $rewardPenalty->setCreatedAt($recordedAt->copy()->startOfDay());
                $rewardPenalty->setUpdatedAt($recordedAt->copy()->startOfDay());
            }
        }

        $rewardPenalty->save();
        $rewardPenalty->load('user:id,name,number');

        return response()->json([
            'data' => [
                'id' => $rewardPenalty->id,
                'user_id' => $rewardPenalty->user_id,
                'user_name' => $rewardPenalty->user?->name,
                'user_number' => $rewardPenalty->user?->number,
                'type' => $rewardPenalty->type,
                'description' => $rewardPenalty->description,
                'amount' => $rewardPenalty->amount,
                'formatted_amount' => number_format($rewardPenalty->amount),
                'recorded_at' => Jalalian::fromCarbon($rewardPenalty->created_at)->format('Y-m-d'),
                'created_at' => $rewardPenalty->created_at,
                'updated_at' => $rewardPenalty->updated_at,
            ],
            'message' => 'رکورد با موفقیت ثبت شد.',
        ], 201);
    }

    private function parseJalaliDate(?string $date): ?Carbon
    {
        if (blank($date)) {
            return null;
        }

        $normalized = convertPersianToEnglish($date);

        try {
            return Jalalian::fromFormat('Y-m-d', $normalized)->toCarbon();
        } catch (Throwable) {
            return null;
        }
    }
}

<?php

namespace Behin\SimpleWorkflowReport\Models;

use BehinUserRoles\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPenalty extends Model
{
    public const TYPE_REWARD = 'reward';
    public const TYPE_PENALTY = 'penalty';

    protected $table = 'rewards_penalties';

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

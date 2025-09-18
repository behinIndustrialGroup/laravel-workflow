<?php

namespace Behin\SimpleWorkflowReport\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PettyCash extends Model
{
    protected $table = 'wf_entity_petty_cashes';
    protected $fillable = ['title', 'amount', 'paid_at', 'from_account'];

    protected $casts = [
        'paid_at' => 'integer',
    ];

    public function getPaidAtAttribute($value): Carbon
    {
        return Carbon::createFromTimestamp($value);
    }
}


<?php

namespace Behin\SimpleWorkflow\Models\Entities;

use BehinUserRoles\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counter_parties extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    public $table = 'wf_entity_counter_parties';

    protected $fillable = ['name', 'account_number', 'description', 'state', 'user_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id
                ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
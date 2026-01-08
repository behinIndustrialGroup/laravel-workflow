<?php

namespace Behin\SimpleWorkflow\Models\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\Jalalian;

class Borrow_requests extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wf_entity_borrow_requests';
    public $incrementing = false; 
    protected $keyType = 'string'; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }

    protected $fillable = [
        'case_id',
        'case_number',
        'item_name',
        'item_id',
        'quantity',
        'requester_id',
        'delivery_id',
        'delivered_at',
        'delivered_at_alt',
        'expected_return_date',
        'expected_return_date_alt',
        'actual_return_date',
        'actual_return_date_alt',
        'status',
        'created_by',
        'updated_by',
        'contributers',
    ];

    protected $casts = [
        'contributers' => 'array',
    ];


    public function getStatusLabelAttribute(): string
    {
        $statuses = config('simpleWorkflowReport.borrow_requests.statuses', []);
        return $statuses[$this->status]['label'] ?? $this->status;
    }

    public function getReturnConfirmation(): ?array
    {
        $contributors = $this->contributers;
        if (is_string($contributors)) {
            $decoded = json_decode($contributors, true);
            $contributors = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        if (!is_array($contributors)) {
            return null;
        }

        return $contributors['return_confirmation'] ?? null;
    }

    public function setReturnConfirmation(array $confirmation): void
    {
        $contributors = $this->contributers;
        if (is_string($contributors)) {
            $decoded = json_decode($contributors, true);
            $contributors = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        if (!is_array($contributors)) {
            $contributors = [];
        }

        $contributors['return_confirmation'] = $confirmation;
        $this->contributers = $contributors;
    }

  
}

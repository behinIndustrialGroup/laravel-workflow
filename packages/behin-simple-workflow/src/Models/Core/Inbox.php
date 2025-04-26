<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Inbox extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_inbox';


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'task_id',
        'case_id',
        'actor',
        'status',
        'case_name'
    ];

    public function getTimeStatusAttribute()
    {
        if ($this->task && $this->task->duration) {
            $createdAt = \Carbon\Carbon::parse($this->created_at);
            $now = \Carbon\Carbon::now();
            $elapsedMinutes = $createdAt->diffInMinutes($now);
            $elapsedMinutes = round($elapsedMinutes, 2);
            if ($elapsedMinutes > $this->task->duration) {
                return "<span style='color: red;'>{$elapsedMinutes} ". trans('fields.Expired') . "</span>"; // زمان گذشته
            } else {
                return "<span style='color: green;'>{$elapsedMinutes} ". trans('fields.Rest') . "</span>"; // هنوز در زمان
            }
        } else {
            return "<span style='color: green;'></span>"; // بدون محدودیت
        }
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function case()
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor');
    }
}

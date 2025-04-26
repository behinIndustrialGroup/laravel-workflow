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
            $diff = $elapsedMinutes - $this->task->duration;
            if ($diff > 0) {
                if($diff > 3600){
                    $diff = round($diff / 3600, 2);
                    return "<span style='color: red;'>{$diff} d ". trans('fields.Expired') . "</span>"; // زمان گذشته
                }
                if($diff > 60){
                    $diff = round($diff / 60);
                    return "<span style='color: red;'>{$diff} h ". trans('fields.Expired') . "</span>"; // زمان گذشته
                }
                return "<span style='color: red;'>{$diff} m ". trans('fields.Expired') . "</span>"; // زمان گذشته
            } else {
                $diff = $this->task->duration - $elapsedMinutes;
                if($diff > 3600){
                    $diff = round($diff / 3600, 2);
                    return "<span style='color: green;'>{$diff} d ". trans('fields.Rest') . "</span>"; // هنوز در زمان
                }
                if($diff > 60){
                    $diff = round($diff / 60);
                    return "<span style='color: green;'>{$diff} h ". trans('fields.Rest') . "</span>"; // هنوز در زمان
                }
                return "<span style='color: green;'>{$diff} m ". trans('fields.Rest') . "</span>"; // هنوز در زمان
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

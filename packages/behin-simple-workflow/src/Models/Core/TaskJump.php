<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class TaskJump extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_task_jumps';

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
        'next_task_id',
    ];

    public function task(){
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function nextTask(){
        return $this->belongsTo(Task::class, 'next_task_id');
    }

    
}

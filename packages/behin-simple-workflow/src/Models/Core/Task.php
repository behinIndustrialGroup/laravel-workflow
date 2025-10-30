<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Task extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_task';

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
        'process_id',
        'name',
        'type',
        'executive_element_id',
        'parent_id',
        'next_element_id',
        'assignment_type',
        'is_preview',
        'show_save_button',
        'show_reminder_button',
        'case_name',
        'color',
        'background',
        'duration',
        'order',
        'timing_type',
        'timing_value',
        'timing_key_name',
        'number_of_task_to_back',
        'script_before_open',
        'allow_cancel',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'show_save_button' => 'boolean',
        'show_reminder_button' => 'boolean',
    ];

    public function getStyledNameAttribute()
    {
        $color = $this->color ?? '#000000'; // رنگ پیش‌فرض مشکی
        $background = $this->background ?? 'transparent'; // بک‌گراند پیش‌فرض
        $name = e($this->name); // جلوگیری از مشکلات امنیتی XSS

        return "<span class='badge' style='color: {$color}; background: {$background}; padding:2px 4px; border-radius:4px;'>{$name}</span>";
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    // رابطه بازگشتی برای فرزندان
    public function children()
    {
        // return $this->hasMany(Task::class, 'parent_id');
        return Task::where('parent_id', $this->id)->whereNot('id', $this->id)->orderBy('order', 'asc')->get();
    }

    public function errors()
    {
        return TaskController::TaskHasError($this->id);
    }

    public function executiveElement()
    {
        if ($this->type == 'form') {
            return FormController::getById($this->executive_element_id);
        }
        if ($this->type == 'script') {
            return ScriptController::getById($this->executive_element_id);
        }
        if ($this->type == 'condition') {
            return ConditionController::getById($this->executive_element_id);
        }
    }

    public function nextTask()
    {
        return TaskController::getById($this->next_element_id);
    }

    public function actors()
    {
        return $this->hasMany(TaskActor::class, 'task_id');
    }

    public function jumps()
    {
        return $this->hasMany(TaskJump::class, 'task_id');
    }
}

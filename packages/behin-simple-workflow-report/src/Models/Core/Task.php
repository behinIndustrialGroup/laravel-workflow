<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Task extends Model
{
    use HasFactory;
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
        'case_name',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    // رابطه بازگشتی برای فرزندان
    public function children()
    {
        // return $this->hasMany(Task::class, 'parent_id');
        return Task::where('parent_id', $this->id)->whereNot('id', $this->id)->get();
    }

    public function executiveElement(){
        if($this->type == 'form'){
            return FormController::getById($this->executive_element_id);
        }
        if($this->type == 'script'){
            return ScriptController::getById($this->executive_element_id);
        }
        if($this->type == 'condition'){
            return ConditionController::getById($this->executive_element_id);
        }
    }

    public function nextTask(){
        return TaskController::getById($this->next_element_id);
    }

    public function actors(){
        return $this->hasMany(TaskActor::class, 'task_id');
    }
}

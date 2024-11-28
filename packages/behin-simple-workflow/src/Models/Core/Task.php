<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\FormController;
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
        'parent_id'
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

    public function executiveElementId(){
        if($this->type == 'form'){
            return FormController::getById($this->executive_element_id);
        }
    }
}

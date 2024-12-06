<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Inbox extends Model
{
    use HasFactory;
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


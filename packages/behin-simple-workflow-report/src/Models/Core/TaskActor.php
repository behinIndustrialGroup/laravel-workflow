<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskActor extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $table = 'wf_task_actor';
    protected $fillable = [
        'task_id',
        'actor',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor');
    }

}

<?php

namespace TodoList\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'creator', 'user_id', 'task', 'description', 'reminder_date', 'due_date', 'done'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(){
        return $this->hasMany(TodoFile::class, 'task_id');
    }
}

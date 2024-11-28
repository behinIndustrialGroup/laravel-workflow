<?php

namespace TodoList\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodoFile extends Model
{
    protected $table = 'todos_files';

    use HasFactory, SoftDeletes;

    protected $fillable = ['task_id', 'file_path', 'file_size', 'file_type', 'status'];

    public function todo(){
        return $this->belongsTo(Todo::class, 'task_id');
    }
}

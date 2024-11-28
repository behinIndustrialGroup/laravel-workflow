<?php

namespace BehinProcessMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMTask extends Model
{
    use HasFactory;
    public $table = 'pm_tasks';
    protected $fillable = [
        'process_uid', 'task_uid', 'task_title', 'dynaform'
    ];
    
}

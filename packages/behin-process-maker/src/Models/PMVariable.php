<?php

namespace BehinProcessMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMVariable extends Model
{
    use HasFactory;
    public $table = 'pm_variables';
    protected $fillable = [
        'process_uid', 'var_uid', 'var_title', 'type', 'accepted_value', 'default_value'
    ];
    
}

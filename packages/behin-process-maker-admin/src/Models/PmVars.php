<?php

namespace BehinProcessMakerAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PmVars extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'pm_vars';
    protected $fillable = [
        'process_id', 'case_id', 'key', 'value'
    ];
    
}

<?php

namespace BehinProcessMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMCase extends Model
{
    use HasFactory;
    public $table = 'pm_cases';
    protected $fillable = [
        'process_id', 'case_id', 'case_name'
    ];
    
}

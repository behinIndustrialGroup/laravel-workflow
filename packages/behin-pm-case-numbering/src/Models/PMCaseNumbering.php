<?php

namespace Behin\PMCaseNumbering\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMCaseNumbering extends Model
{
    use HasFactory;
    public $table = 'pm_case_numbering';
    protected $fillable = [
        'process_id', 'count', 'api_key'
    ];
    
}

<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseNumbering extends Model
{
    use HasFactory;

    public $table = 'wf_case_numbering';

    protected $fillable = [
        'prefix',
        'count',
    ];
}


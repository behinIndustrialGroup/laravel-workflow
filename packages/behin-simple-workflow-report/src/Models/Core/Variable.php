<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Variable extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $table = 'wf_variables';

    protected $fillable = [
        'process_id',
        'case_id',
        'key',
        'value'
    ];

}


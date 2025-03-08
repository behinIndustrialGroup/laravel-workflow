<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;


class Entity extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_entities';


    protected $fillable = [
        'name',
        'description',
        'columns',
        'uses',
        'class_contents',
        'model_name',
        'namespace',
    ];

}

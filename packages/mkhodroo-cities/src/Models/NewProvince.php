<?php

namespace Mkhodroo\Cities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewProvince extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name'
    ];

}

<?php

namespace Mkhodroo\Cities\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public $table = "province";
    public $timestamps = false;
    protected $fillable = [
        'name', 'code'
    ];

}

<?php 

namespace Mkhodroo\Cities\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $table = "cities";
    protected $fillable = [
        'province', 'city', 'latitude', 'longitude'
    ];

    // function role() {
    //     return 
    // }
}
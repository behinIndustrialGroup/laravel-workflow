<?php 

namespace BehinUserRoles\Models;

use Illuminate\Database\Eloquent\Model;

class Method extends Model
{
    public $table = "behin_methods";
    protected $fillable = [
        'name', 'disable'
    ];

    // function role() {
    //     return 
    // }
}
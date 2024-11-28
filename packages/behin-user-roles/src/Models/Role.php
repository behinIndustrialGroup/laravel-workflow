<?php 

namespace BehinUserRoles\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $table = "behin_roles";
    protected $fillable = [
        'name'
    ];

    // function role() {
    //     return 
    // }
}
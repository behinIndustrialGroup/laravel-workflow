<?php 

namespace BehinUserRoles\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public $table = "departments";
    protected $fillable = [
        'name', 'manager', 'parent_id'
    ];

    // function role() {
    //     return 
    // }
}
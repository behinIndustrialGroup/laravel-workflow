<?php 

namespace BehinUserRoles\Models;

use Illuminate\Database\Eloquent\Model;

class UserDepartment extends Model
{
    public $table = "users_departments";
    protected $fillable = [
        'user_id', 'department_id'
    ];

    public function user(){
        return User::find($this->user_id);
    }

    public function department(){
        return Department::find($this->department_id);
    }

    // function role() {
    //     return 
    // }
}
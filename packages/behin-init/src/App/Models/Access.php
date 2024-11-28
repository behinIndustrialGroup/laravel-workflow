<?php 

namespace BehinInit\App\Models;

use BehinUserRoles\Models\Method;
use BehinUserRoles\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    public $table = "behin_access";
    protected $fillable = [
        'role_id', 'method_id', 'access'
    ];

    function role() {
        return Role::find($this->role_id);
    }

    function method() {
        return Method::find($this->method_id);
    }
}
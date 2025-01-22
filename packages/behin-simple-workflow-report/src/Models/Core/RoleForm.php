<?php

namespace Behin\SimpleWorkflowReport\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Process;
use BehinUserRoles\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleForm extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $table = 'wf_process_role_form_control';
    protected $fillable = [
        'role_id',
        'process_id',
        'summary_form_id',

    ];

    public function role(){
        return Role::find($this->role_id);
    }

    public function process(){
        return Process::find($this->process_id);
    }

    public function summary(){
        return Form ::find($this->summary_form_id);
    }

}

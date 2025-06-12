<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Cases;
 class Part_reports extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_part_reports'; 
    protected $fillable = ['case_id', 'case_number', 'fix_report', 'done_at', 'repair_duration', 'see_the_problem', 'registered_by', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
function case(){
    return Cases::find($this->case_id);
}}
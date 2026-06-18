<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Behin\SimpleWorkflow\Models\Core\Cases;
 class Repair_reports extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_repair_reports'; 
    protected $fillable = ['creator', 'case_id', 'case_number', 'report', 'start_date', 'start_time', 'end_date', 'end_time', 'mapa_expert', 'mapa_expert_head', 'device', 'process', 'device_id', 'customer_approval', 'request_id', 'final_test_and_result', 'duration', 'repair_is_approved', 'was_backups_taken', 'parameter_backup', 'pcparam_backup', 'sram_backup', 'sysfile_backup', 'prog_backup', 'reason_of_not_taking_backup', 'need_next_visit', 'next_visit_description', 'part_left_from_customer_location', 'job_rank', 'customer_validation_code', 'customer_signature', 'mapa_expert_companions', 'customer_representative_mobile', 'customer_representative_name', 'round_trip_time',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
function case(){
    return Cases::find($this->case_id);
}

function device(){
    return Devices::find($this->device);
}}
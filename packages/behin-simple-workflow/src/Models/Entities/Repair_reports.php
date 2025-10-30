<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Repair_reports extends Model 
{ 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_repair_reports'; 
    protected $fillable = ['creator', 'case_id', 'case_number', 'report', 'start_date', 'start_time', 'end_date', 'end_time', 'mapa_expert', 'mapa_expert_head', 'device', 'process', 'device_id', 'customer_approval', 'request_id', 'final_test_and_result', 'duration', 'repair_is_approved', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
}
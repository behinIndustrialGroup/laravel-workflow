<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Devices extends Model 
{ 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_devices'; 
    protected $fillable = ['case_id', 'case_number', 'name', 'model', 'control_system', 'control_system_model', 'serial', 'has_electrical_map', 'mapa_serial', 'mapa_expert_head', 'repair_is_approved', 'dispatched_expert_needed', 'dispatched_expert', 'mapa_expert_companions', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
}
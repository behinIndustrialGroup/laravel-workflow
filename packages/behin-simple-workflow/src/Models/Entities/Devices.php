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
    protected $fillable = ['request_id', 'name', 'model', 'control_system', 'control_system_model', 'has_electrical_map', 'mapa_serial', 'serial', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
}
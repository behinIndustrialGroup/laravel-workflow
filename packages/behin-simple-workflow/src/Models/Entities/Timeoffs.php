<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Timeoffs extends Model 
{ 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_timeoffs'; 
    protected $fillable = ['user', 'type', 'duration', 'request_day', 'request_month', 'request_year', 'approved', 'uniqueId', 'start_year', 'start_month', 'start_day', 'end_year', 'end_month', 'end_day', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
}
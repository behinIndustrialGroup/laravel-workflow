<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes; use Behin\SimpleWorkflow\Models\Entities\WfCase;
 class Goods_in extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_goods_in'; 
    protected $fillable = ['case_id', 'case_number', 'date_time_in', 'date_time_in_alt', 'item_name', 'item_code', 'quantity', 'unit', 'sender_or_receiver_name', 'vehicle_plate', 'driver_name', 'receiver', 'notes', 'in_or_out',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }

    public function case()
    {
        return DB::table('wf_cases')->where('id', $this->case_id)->first();
    }
}
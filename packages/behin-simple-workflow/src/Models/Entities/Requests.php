<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Requests extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_requests'; 
    protected $fillable = ['case_id', 'user_id', 'powerhouse_place_info_id', 'subsidy_status', 'contractor_id', 'technician_head_id', 'last_evaluation_result', 'min_price', 'max_price', 'final_price', 'final_price_confirmation_by_customer', 'confirm_contract_by_customer', 'confirm_contract_by_contractor', 'inspector_id', 'certificate_id', 'tracking_code', 'number_of_used_panel', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
}
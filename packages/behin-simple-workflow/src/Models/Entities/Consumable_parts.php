<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes; use Behin\SimpleWorkflow\Models\Core\Cases;
 class Consumable_parts extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_consumable_parts'; 
    protected $fillable = ['case_id', 'case_number', 'product_id', 'product_name', 'requested_quantity', 'delivered_quantity', 'required_quantity_to_purchase', 'purchased_quantity', 'consumable_part_status', 'consumable_part_state', 'returned_quantity', 'amount', 'supply_source', 'invoice_number', 'invoice_date', 'invoice_date_alt', 'external_buying_is_done',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function product(){
    return Products::find($this->product_id) ?? 'یافت نشد';
}

public function case(){
    return Cases::where('number', $this->case_number)->first();
}

public function usedQuantity(){
    return $this->delivered_quantity - $this->returned_quantity;
}

public function formatedAmount(){
    return number_format($this->amount);
}}
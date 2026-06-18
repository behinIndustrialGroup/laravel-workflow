<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes;use Behin\SimpleWorkflow\Models\Core\Cases;
 class OnCreditPayment extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_OnCreditPayment'; 
    protected $fillable = ['case_id', 'case_number', 'process_id', 'process_name', 'payment_type', 'amount', 'date', 'account_number', 'account_name', 'cheque_number', 'cheque_due_date', 'bank_name', 'invoice_number', 'cheque_receiver', 'is_passed',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function case(){
    return Cases::find($this->case_id);
}}
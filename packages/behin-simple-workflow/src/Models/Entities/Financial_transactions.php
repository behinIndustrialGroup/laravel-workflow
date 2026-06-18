<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes;use Behin\SimpleWorkflow\Models\Entities\Counter_parties;
 class Financial_transactions extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_financial_transactions'; 
    protected $fillable = ['case_id', 'case_number', 'financial_type', 'financial_method', 'counterparty_id', 'counterparty_name', 'amount', 'description', 'invoice_or_cheque_number', 'transaction_or_cheque_due_date', 'destination_account_name', 'destination_account_number', 'transaction_or_cheque_due_date_alt', 'is_passed', 'destination_account_id', 'auto_financial_transaction_id',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
function counterparty(){
    return Counter_parties::find($this->counterparty_id);
}

function amount(){
    return number_format($this->amount);
}}
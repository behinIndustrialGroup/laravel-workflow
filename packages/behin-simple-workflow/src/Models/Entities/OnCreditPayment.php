<?php
namespace Behin\SimpleWorkflow\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnCreditPayment extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'wf_on_credit_payments';

    protected $fillable = [
        'case_number',
        'case_id',
        'process_id',
        'process_name',
        'payment_type',
        'amount',
        'date',
        'account_number',
        'account_name',
        'cheque_number',
        'cheque_due_date',
        'bank_name',
        'invoice_number',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
        });
    }
}

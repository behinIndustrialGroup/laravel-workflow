<?php
namespace Behin\SimpleWorkflow\Models\Entities;

use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financials extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_entity_financials';

    protected $fillable = [
        'description',
        'cost',
        'payment',
        'destination_account',
        'destination_account_name',
        'payment_date',
        'payment_method',
        'case_number',
        'process_id',
        'process_name',
        'payment_after_completion',
        'case_id',
        'fix_cost_date',
        'destination_account_2',
        'destination_account_name_2',
        'destination_account_3',
        'destination_account_name_3',
        'fix_cost_type',
        'cost2',
        'cost3',
        'cheque_due_date',
        'cheque_number',
        'cheque_receiver',
        'is_passed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
        });
    }

    public function process()
    {
        return Process::find($this->process_id);
    }

    public function case()
    {
        return Cases::find($this->case_id);
    }
}

<?php

namespace Behin\SimpleWorkflow\Models\Entities;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class CasesManual extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_cases';


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'process_id',
        'number',
        'name',
        'creator',
        'parent_id'
    ];

    public function createName()
    {
        $case = Cases::find($this->id);
        if($case->process_id == '879e001c-59d5-4afb-958c-15ec7ff269d1'){ // تعمیرات
            $customer = Case_customer::where('case_id', $this->id)->orwhere('case_number', $this->number)->first();
            $name = '';
            if($customer){
                $name = $customer->fullname;
            }
            $device = Devices::where('case_id', $this->id)->orwhere('case_number', $this->number)->first();
            if($device){
                $name .= ' | ' . $device->name;
            }
            return $name;
        }
        return '';
    }
}

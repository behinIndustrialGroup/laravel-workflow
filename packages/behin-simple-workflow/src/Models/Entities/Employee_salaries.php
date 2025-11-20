<?php

namespace Behin\SimpleWorkflow\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Employee_salaries extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public $table = 'wf_entity_employee_salaries';

    protected $fillable = ['user_id', 'insurance_salary', 'real_salary'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id
                ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
        });
    }
}

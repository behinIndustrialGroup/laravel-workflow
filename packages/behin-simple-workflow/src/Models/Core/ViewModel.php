<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ViewModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_view_models';

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
        'name',
        'api_key',
        'entity_id',
        'entity_name',
        'max_number_of_rows',
        'default_fields',
        'show_as',
        'allow_create_row',
        'create_form',
        'create_form_fields',
        'show_create_form_at_the_end',
        'allow_update_row',
        'update_form',
        'update_form_fields',
        'which_rows_user_can_update',
        'allow_delete_row',
        'which_rows_user_can_delete',
        'allow_read_row',
        'read_form',
        'read_form_fields',
        'which_rows_user_can_read',
        'show_rows_based_on',
        'script_after_create',
        'script_after_update',
        'script_after_delete',
        'script_before_show_rows',
    ];

    public function entity(){
        return $this->belongsTo(Entity::class);
    }

    protected $casts = [
        'which_rows_user_can_update' => 'array',
        'which_rows_user_can_delete' => 'array',
        'which_rows_user_can_read' => 'array',
    ];
}

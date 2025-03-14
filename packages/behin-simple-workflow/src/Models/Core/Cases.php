<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Cases extends Model
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

    public function variables()
    {
        return VariableController::getVariablesByCaseId($this->id, $this->process_id);
    }

    public function getVariable($name)
    {
        return VariableController::getVariable($this->process_id, $this->id, $name)?->value;
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }


    public function creator()
    {
        return User::find($this->creator);
    }

    public function whereIs(){
        $childCaseId = Cases::where('parent_id', $this->id)->get()->pluck('id')->toArray();
        return Inbox::where(function($query) use($childCaseId){
            $query->where('case_id', $this->id)->orWhereIn('case_id', $childCaseId);
        })->whereNotIn('status', ['done', 'doneByOther', 'canceled'])->get();
    }

    public function previousTask(){
        return Inbox::where('case_id', $this->id)->whereIn('status', ['done'])->orderBy('created_at', 'desc')->first();
    }
}

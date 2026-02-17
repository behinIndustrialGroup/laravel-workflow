<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Behin\SimpleWorkflow\Models\Entities\Customers;
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
        'parent_id',
        'status'
    ];

    public function variables()
    {
        return VariableController::getVariablesByCaseId($this->id);
    }

    public function getVariable($name)
    {
        return VariableController::getVariable($this->process_id, $this->id, $name)?->value;
    }

    public function saveVariable($name, $value)
    {
        return VariableController::save($this->process_id, $this->id, $name, $value);
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }


    public function creator()
    {
        return User::find($this->creator);
    }

    public function copyVariableFrom($parentCaseId, $prefix = '', $variables = null){
        $parentCase = Cases::find($parentCaseId);
        foreach($parentCase->variables() as $variable){
            if(!$variables){
                VariableController::save($this->process_id, $this->id, $prefix . $variable->key, $variable->value);
            }else{
                if(in_array($variable->key, $variables)){
                    VariableController::save($this->process_id, $this->id, $prefix. $variable->key, $variable->value);
                }
            }
        }
    }

    public function whereIs(){
        $childCaseId = Cases::where('number', $this->number)->pluck('id')->toArray();
        $rows = Inbox::WhereIn('case_id', $childCaseId)->whereNotIn('status', ['done', 'doneByOther', 'canceled'])->get();

        return $rows;
    }

    public function previousTask(){
        return Inbox::where('case_id', $this->id)->whereIn('status', ['done'])->orderBy('created_at', 'desc')->first();
    }

    public function getHistoryAttribute(){
         return "<a title='". trans('fields.History') ."' href='". route('simpleWorkflow.inbox.caseHistoryView', ['caseNumber' => $this->number]) ."'><i class='fa fa-history'></i></a>";
    }

    public function getHistoryList(){
        return InboxController::caseHistoryList($this->number);
    }

    public function children(){
        return Cases::where('parent_id', $this->id)->get();
    }

    public function parent(){
        return Cases::find($this->parent_id);
    }

    public function customerName(){
        if($this->getVariable('customer_id')){
            return Customers::find($this->getVariable('customer_id'))?->name;
        }
        return $this->getVariable('customer_workshop_or_ceo_name');
    }

}

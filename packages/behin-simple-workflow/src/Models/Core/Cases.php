<?php

namespace Behin\SimpleWorkflow\Models\Core;

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
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
        $childCaseId = Cases::where('number', $this->number)->get()->pluck('id')->toArray();
        $rows = Inbox::where(function($query) use($childCaseId){
            $query->where('case_id', $this->id)->orWhereIn('case_id', $childCaseId);
        })->whereNotIn('status', ['done', 'doneByOther', 'canceled'])->get();

        if ($rows->isEmpty()) {
            if($this->process_id == '4bb6287b-9ddc-4737-9573-72071654b9de'){
                InboxController::create('26bc2853-99f0-4959-a4db-0daee5e894f9', $this->id, null, 'new');
            }
            if($this->process_id == '35a5c023-5e85-409e-8ba4-a8c00291561c'){
                InboxController::create('0be8e2a9-2a76-4738-a090-83a9885b17e7', $this->id, null, 'new');
            }
            if($this->process_id == 'ab17ef68-6ec7-4dc8-83b0-5fb6ffcedc50'){
                InboxController::create('43f016cf-0cdf-487d-8d1d-df1f1ca2d543', $this->id, null, 'new');
            }
            // return [(object) [
            //     'archive' => 'yes',
            //     'task' => (object) [
            //         'styled_name' => "<span style='color: #ffffff; background: #007a41; padding:2px 4px; border-radius:4px;'>پایان کار</span>",
            //     ],
            //     'actor' => '',
            // ]];
        }
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

}

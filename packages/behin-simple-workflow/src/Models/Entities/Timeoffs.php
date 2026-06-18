<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Timeoffs extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_timeoffs'; 
    protected $fillable = ['user', 'type', 'duration', 'request_day', 'request_month', 'request_year', 'approved', 'uniqueId', 'start_year', 'start_month', 'start_day', 'end_year', 'end_month', 'end_day', 'start_timestamp', 'end_timestamp', 'case_id', 'case_number', 'request_timestamp', 'description', 'approved_by', ]; 

public function user(){
    return getUserInfo($this->user);
}

public function case(){
    return Cases::find($this->case_id);
}}
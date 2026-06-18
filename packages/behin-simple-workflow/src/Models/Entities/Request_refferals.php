<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Request_refferals extends Model 
{ 
    public $table = 'wf_entity_request_refferals'; 
    protected $fillable = ['request_id', 'refferal_date', 'refferal_time', ]; 
}
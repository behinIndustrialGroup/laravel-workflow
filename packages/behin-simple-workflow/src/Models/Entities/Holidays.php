<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Holidays extends Model 
{ 
    public $table = 'wf_entity_holidays'; 
    protected $fillable = ['date', 'description', ]; 
}
<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Repair_reports extends Model 
{ 
    public $table = 'wf_entity_repair_reports'; 
    protected $fillable = ['case_number', 'reports', 'start_date', 'start_time', 'end_date', ]; 
}
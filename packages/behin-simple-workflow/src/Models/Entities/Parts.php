<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Parts extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_parts'; 
    protected $fillable = ['case_id', 'case_number', 'device_id', 'name', 'serial', 'mapa_expert_head', 'mapa_expert', 'refer_to_unit', 'other_parts', 'special_parts', 'power', 'repair_duration', 'see_the_problem', 'fix_report', 'final_result_and_test', 'test_possibility', 'problem_seeing', 'final_result', 'sending_for_test_and_troubleshoot', 'test_in_another_place', 'job_rank', 'repair_is_approved', 'mapa_serial', 'initial_part_pic', 'has_attachment', 'dispatched_expert_needed', 'dispatched_expert', 'mapa_expert_companions', 'dispatched_expert_description', 'done_at', 'attachment_image', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function mapaExpertHead(){
    return getUserInfo($this->mapa_expert_head);
}

public function mapaExpert(){
    return getUserInfo($this->mapa_expert);
}

public function reports(){
    return Part_reports::where('part_id', $this->id)->get();
}

public function experts()
{
    return Part_reports::select('registered_by', \DB::raw('SUM(repair_duration) as total_duration'))
        ->where('part_id', $this->id)
        ->groupBy('registered_by')
        ->get()->each(function($row){
            $row->name = getUserInfo($row->registered_by)?->name;
        });
}}
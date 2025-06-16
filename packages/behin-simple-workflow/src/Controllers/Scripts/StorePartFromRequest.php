<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\InboxController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities;
use BehinFileControl\Controllers\FileController;



class StorePartFromRequest extends Controller
{
    private $case;
    public function __construct()
    {
        
    }

    public function execute(Request $request = null)
    {
        $caseId = $request->caseId;
        $case = $this->case = CaseController::getById($caseId);
        $name = $request->part_name;
        $head = $request->mapa_expert_head;
        $unit = $request->refer_to_unit;
        if(!$name){
            return response("نام قطعه خالیست", 402);
        }
        if(!$head){
            return response("سرپرست تعمیرات خالی است", 402);
        }
        if(!$unit){
            return "ارجاع به واحد خالیست";
        }
        $initial_part_pic = null;
        if($request->initial_part_pic){
            $result = FileController::store($request->initial_part_pic, 'simpleWorkflow');
            if ($result['status'] == 200) {
                $initial_part_pic = $result['dir'];
            }
        }
        $attachmentImage = null;
        if($request->attachment_image){
            $result = FileController::store($request->attachment_image, 'simpleWorkflow');
            if ($result['status'] == 200) {
                $attachmentImage = $result['dir'];
            }
        }
        $vars = [
            'case_id'=> $case->id,
            'case_number' => $case->number,
            // 'device_id' => $request->device_id,
            'name' => $name,
            'serial' => $request->part_serial,
            'mapa_serial' => $request->mapa_serial,
            'initial_part_pic' => $initial_part_pic,
            'mapa_expert_head' => $head,
            'refer_to_unit' => $unit,
            'has_attachment' => $request->has_attachment,
            'attachment_image' => $attachmentImage
        ];
        $part = Entities\Parts::updateOrCreate($vars);
        $case->saveVariable('part_id', $part->id);
        
        //خالی کردن مقادیر قطعه
        $case->saveVariable('part_name', '');
        $case->saveVariable('part_serial', '');
        $case->saveVariable('mapa_expert_head_for_internal_process', '');
        $case->saveVariable('refer_to_unit', '');
        $case->saveVariable('initial_part_pic', '');
        $case->saveVariable('mapa_serial', '');
        $case->saveVariable('attachment_image', '');
        return "با موفقیت ثبت شد";
    }
}
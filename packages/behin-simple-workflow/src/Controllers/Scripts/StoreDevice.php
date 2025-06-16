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



class StoreDevice extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $case = $this->case;
        $mapaSerial = $case->getVariable('mapa_serial');
        // if($mapaSerial){
        //     $v = ExternalMapaSerialValidation::execute($mapaSerial);
        //     if($v){
        //         return $v;
        //     }
        // }
        
        $deviceId = $case->getVariable('device_id');
        if($deviceId){
           $device =  Entities\Devices::find($deviceId);
        }else{
            $device = new Entities\Devices();
        }
        
        $device->case_id= $case->id;
        $device->case_number = $case->number;
        $device->name = $case->getVariable('device_name');
        $device->model = $case->getVariable('device_model');
        $device->control_system = $case->getVariable('device_control_system');
        $device->serial = $case->getVariable('device_serial');
        $device->mapa_serial = $case->getVariable('mapa_serial');
        $device->save();
        
        $case->saveVariable('device_id', $device->id);
        
    }
}
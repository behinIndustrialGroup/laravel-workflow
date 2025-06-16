<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Devices;

class StoreRepairReport extends Controller
{
    private $case;

    public function __construct($case)
    {
        $this->case = CaseController::getById($case->id);
    }

    public function execute()
    {
        $case = $this->case;
        $caseNumber = $case->number;
        $variables = $case->variables();
        $mapaSerial = $case->getVariable('mapa_serial');
        $mapaSerial = str_replace('-', '/', $mapaSerial);
        
        $v = ExternalMapaSerialValidation::execute($mapaSerial);
        if($v){
            return $v;
        }
        

        // گرفتن شناسه دستگاه از متغیرها یا ساخت دستگاه جدید در صورت نبود
        $deviceId = $case->getVariable('device_id');
        $device = $deviceId ? Devices::find($deviceId) : Devices::where('case_id', $case->id)->first();

        if (!$device) {
            $device = Devices::create([
                'case_id'                => $case->id,
                'case_number'            => $caseNumber,
                'name'                   => $case->getVariable('device_name'),
                'model'                  => $case->getVariable('device_model'),
                'control_system'         => $case->getVariable('device_control_system'),
                'control_system_model'   => $case->getVariable('device_control_system_model'),
                'serial'                 => $case->getVariable('device_serial'),
                'mapa_serial'            => $mapaSerial,
                'has_electrical_map'     => $case->getVariable('has_electrical_map'),
            ]);
            
        }else{
            $device->name = $case->getVariable('device_name');
            $device->model = $case->getVariable('device_model');
            $device->control_system = $case->getVariable('device_control_system');
            $device->control_system_model = $case->getVariable('device_control_system_model');
            $device->serial = $case->getVariable('device_serial');
            $device->has_electrical_map = $case->getVariable('has_electrical_map');
            $device->mapa_serial = $mapaSerial;
            $device->save();
        }
        

        $mapaExpertId = $case->getVariable('mapa_expert');
        $mapaExpertName = getUserInfo($mapaExpertId)->name ?? '';

        Repair_reports::updateOrCreate(
            [
                'case_id'           => $case->id,
                'case_number'       => $caseNumber,
                'creator'           => $mapaExpertName,
                'report'            => $case->getVariable('fix_report'),
                'start_date'        => $case->getVariable('fix_start_date'),
                'start_time'        => $case->getVariable('fix_start_time'),
                'end_date'          => $case->getVariable('fix_end_date'),
                'end_time'          => $case->getVariable('fix_end_time'),
                'mapa_expert'       => $mapaExpertId,
                'mapa_expert_head'  => $case->getVariable('mapa_expert_head'),
                'mapa_expert_companions' => $case->getVariable('mapa_expert_companions'),
                'device'            => $device->id,
                'was_backups_taken' => $case->getVariable('was_backups_taken'),
                'parameter_backup'  => $case->getVariable('parameter_backup'),
                'pcparam_backup'    => $case->getVariable('pcparam_backup'),
                'sram_backup'       => $case->getVariable('sram_backup'),
                'sysfile_backup'    => $case->getVariable('sysfile_backup'),
                'prog_backup'       => $case->getVariable('prog_backup'),
                'reason_of_not_taking_backup'   => $case->getVariable('reason_of_not_taking_backup'),
                'need_next_visit'   => $case->getVariable('need_next_visit'),
                'next_visit_description' => $case->getVariable('next_visit_description'),
                'part_left_from_customer_location'  => $case->getVariable('part_left_from_customer_location'),
                'customer_validation_code' => $case->getVariable('customer_validation_code'),
                'customer_signature' => $case->getVariable('customer_signature')
            ]
        );
        
    }
}
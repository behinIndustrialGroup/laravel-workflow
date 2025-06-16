<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Devices;

class ExternalMapaSerialValidation extends Controller
{
    private $case;

    public function __construct()
    {
        // $this->case = CaseController::getById($case->id);
    }

    public static function execute($mapaSerial)
    {
        $mapaSerial = convertPersianToEnglish($mapaSerial);
        $explodeMapaSerial = explode("-", $mapaSerial);
        if(count($explodeMapaSerial) == 1){
            $explodeMapaSerial = explode("/", $mapaSerial);
            if(count($explodeMapaSerial) == 1){
                return "سریال مپا را با / یا - وارد کنید";
            }
        }
        if(count($explodeMapaSerial) != 5){
            return "سریال مپا باید شامل 5 بخش باشد که با - یا / جدا شده اند";
        }
        if(strlen($explodeMapaSerial[0]) != 3){
            return "بخش اول باید شامل 3 رقم باشد";
        }
        if(strlen($explodeMapaSerial[1]) != 2){
            return "بخش دوم باید شامل 2 رقم باشد";
        }
        if(strlen($explodeMapaSerial[2]) != 2){
            return "بخش سوم باید شامل 2 رقم باشد";
        }
        if(strlen($explodeMapaSerial[3]) != 2){
            return "بخش چهارم باید شامل 2 رقم باشد";
        }
    }
}
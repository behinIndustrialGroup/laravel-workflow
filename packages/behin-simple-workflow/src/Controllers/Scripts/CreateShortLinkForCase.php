<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Counter_parties;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use ShortenerUrl\Shortener\Http\Controllers\ShortLinkController;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Http\Request;

class CreateShortLinkForCase extends Controller
{
    private $case;

    public function __construct($case)
    {
        $this->case = $case;
    }

    public function execute()
    {
        $case = $this->case;
        $inbox = Inbox::where('task_id', 'a8bf3dce-7b97-4b0d-ae69-50816b79cc2e')->where('case_id', $case->id)->first();
        $mobile = $case->getVariable('customer_mobile');
        if($inbox){
            $url = route('simpleWorkflow.inbox.view', ['inboxId' => $inbox->id]);
            $shortUrl = ShortLinkController::make($url);
            $case->saveVariable('tracking_url', $shortUrl);
            $params = array([
                "name" => "CASE_NUMBER",
                "value" => $case->number
            ],
            [
                'name' => "LINK",
                "value" => $shortUrl
            ]);
            $result = SmsController::sendByTemp($mobile, 632838, $params);
            $result = SmsController::sendByTemp("09376922176", 632838, $params);
            // return json_encode($result);
        }
        
    }
}
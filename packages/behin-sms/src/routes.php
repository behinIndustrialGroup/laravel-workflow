<?php

use Behin\Sms\Controllers\SendSmsController;
use Behin\Sms\Controllers\SmsController;
use Illuminate\Support\Facades\Route;
use Mkhodroo\SmsTemplate\Controllers\SmsTemplateController;
use Mkhodroo\Voip\Controllers\VoipController;

Route::name('sms.')->prefix('sms')->group(function(){
    Route::get('{sms_id}/{to}/{params?}', [SmsTemplateController::class, 'send'])->name('send');
    Route::post('send', [SmsController::class, 'send'])->name('send');
    Route::get('test', function(){
        $url = 'http://api.payamak-panel.com/post/Send.asmx/SendSimpleSMS2?';
        $data=array(
            'username' =>"09376265059",
            'password'=>"R59DG",
            'to' =>"09376922176",
            'from' => "50004001265059",
            "text" =>"سلام عرض ادب",
            "isflash" => 'false'
        );
        $post_data = http_build_query($data);
        return $url . $post_data;
    })->name('send');
});

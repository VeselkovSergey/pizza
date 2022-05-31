<?php

namespace App\Http\Controllers\SMSSender;

use App\Helpers\ResultGenerate;
use App\Services\SMS\SMSService;

class SMSSenderController extends \App\Http\Controllers\Controller
{
    public function Index()
    {
        return view('sms-sender.index');
    }

    /**
     * @throws \Exception
     */
    public function SendSMS()
    {
        $phone = request()->post('phone');
        $text = request()->post('text');
        $deviceID = request()->post('deviceID');
        $secret = request()->post('secret');
        try {
            SMSService::Send($deviceID, $secret, $phone, $text);
            return 'СМС успешно отправлено';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
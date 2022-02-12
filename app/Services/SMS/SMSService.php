<?php

namespace App\Services\SMS;

class SMSService
{
    /**
     * @throws \Exception
     */
    public static function SendSMS($phone, $text)
    {
        $phone = preg_replace("/[^0-9]/", '', $phone);
        $phone[0] = 8;

        if (strlen($phone) < 11) {
            throw new \Exception('Не верный формат номера');
        }

        $time = time();
        $deviceID = "d2hDR_zV6Bg:APA91bF3EcRaY35Ox9IU7DL2bbIQ0JlgkLd2hAVcxkfuL4tvJRhyFjXRKBURPvvySau7-MAJfUBldEUxUaLMpr4zX5Ewkr-Q_7esWZK79-70dX3DwjcRb_JVj9xxSuVfc_OcolVOTe0D";
        $secret = "0031ad0e-affb-4f25-b013-96ba084ab74f";

        $secret = md5($secret . $time);

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array (
            'to' => $deviceID,
            'data' => array (
                "to" => $phone,
                "time" => $time,
                "secret" => $secret,
                "message" => $text,
            )
        );
        $fields = json_encode ( $fields );

        $headers = array (
            'Authorization: key=' . 'AAAAidsox18:APA91bH0mp7a3tZyjempw4iYaAvHg0s2c64WS30m8ZWKPMN89636_hjZQAlnG3dvKLMt4TBDmiFsHehyuENLJATls8_LljJAv8ppV3eg16YKanuTvbeq99zOzsEquSuH7SuSxhVG9Wbp',
            'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        $result = curl_exec ( $ch );

        curl_close ( $ch );

        return $result;
    }
}
<?php

namespace App\Services\SMS;

use App\Models\User;

class SMSService
{
    /**
     * @throws \Exception
     */
    public static function Send($deviceID, $secret, $phone, $text)
    {
        $phone = preg_replace("/[^0-9]/", '', $phone);
        $phone[0] = 8;

        if (strlen($phone) < 11) {
            throw new \Exception('Не верный формат номера');
        }

        $time = time();
//        $deviceID = "d2hDR_zV6Bg:APA91bF3EcRaY35Ox9IU7DL2bbIQ0JlgkLd2hAVcxkfuL4tvJRhyFjXRKBURPvvySau7-MAJfUBldEUxUaLMpr4zX5Ewkr-Q_7esWZK79-70dX3DwjcRb_JVj9xxSuVfc_OcolVOTe0D";       // mi6
//        $secret = "0031ad0e-affb-4f25-b013-96ba084ab74f";       // mi6

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



    public static function SendSmsToUser(User $user, $text)
    {
        /**
         * mi8
         */
//        $deviceId = 'cLVirCPqeHc:APA91bHTXTCAWTJAjBB4JqKZzHGqMgXKrRvz93TNOkuzl9HN7uGhVBLzuku5R8YbyrC-I3wowd-yhH8LHPKSI_spvHrKz69vxQUjcN5w7ilYUS68m6j3DVwn2__bjYh9IOTqBmEPykIe';
//        $secret = '8d526200-8b06-4ef0-bf03-dc8426db21a8';

        /**
         * j1
         */
        $deviceId = 'eN66_HSSQHw:APA91bHe7DP8ztUPpWEtK_mXgV5srQ4x-YYIYTvwe1i0r4HzePMdXU1-qU97hH3VHynCPaLphMdvb0pPNzPiku040W4c_NJZ6OwyrrfXuxNpnRA4Lhu0nBMcTV0H5l39MNOJAOMA2aKx';
        $secret = '47ec1e63-f901-4f73-ba17-443dafc79262';

        self::Send($deviceId, $secret, $user->phone, $text);
    }
}
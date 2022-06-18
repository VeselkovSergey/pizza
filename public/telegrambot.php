<?php

class TelegramApi
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    private function send($method, $text, $chatId)
    {
        $remote = 'https://api.telegram.org/bot' . $this->token . '/' . $method;

        $remote .= '?chat_id=' . $chatId . '&text=' . $text;

        return file_get_contents($remote);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $remote, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
//            CURLOPT_POST => false, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query([
                'text' => $text,
                'chat_id' => $chatId
            ]), // Данные в запросе
        ]);


        return curl_exec($curl);
    }

    public function sendMessage($text, $chatId)
    {
        return $this->send('sendMessage', $text, $chatId);
    }
}

class TelegramBot
{
    public static function incomingRequest()
    {
        $request = file_get_contents('php://input');

        $request = json_decode($request);
        self::sendRequest($request);

        $fromChatId = $request->message->chat->id;
        self::sendRequest($request, $fromChatId);
        self::sendRequest(self::courses(), $fromChatId);
    }

    public static function sendRequest($rawRequest, $chatId = 267236435)
    {
        $telegramApi = new TelegramApi('1913717295:AAH0QLrCiQLyeJt4BVB_sctJR1b5K3SNZYk');
        $telegramApi->sendMessage(json_encode($rawRequest, JSON_UNESCAPED_UNICODE), $chatId);
    }

    public static function courses()
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.124 Safari/537.36 Edg/102.0.1245.44';

        $btcUrl = 'https://www.rbc.ru/crypto/data/graph/166026/day/1/?_=' . time();
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $btcUrl, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
//            CURLOPT_POST => false, // Метод POST
            CURLOPT_USERAGENT => $userAgent

        ]);
        $btcResult = array_pop(json_decode(curl_exec($curl))->result->data)[1];

        $usdUrl = 'https://quote.rbc.ru/data/ticker/graph/59111/d/?_=' . time();
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $usdUrl, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
//            CURLOPT_POST => false, // Метод POST
            CURLOPT_USERAGENT => $userAgent

        ]);
        $usdResultArray = json_decode(curl_exec($curl))->result->data;
        $usdResult = null;
        foreach ($usdResultArray as $key => $item) {
            if ($key !== 0 && is_null($item[4])) {
                break;
            } else {
                $usdResult = $item;
            }
        }

        $eurUrl = 'https://quote.ru/data/ticker/graph/59090/d/?_=' . time();
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $eurUrl, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
//            CURLOPT_POST => false, // Метод POST
            CURLOPT_USERAGENT => $userAgent

        ]);
        $eurResultArray = json_decode(curl_exec($curl))->result->data;
        $eurResult = null;
        foreach ($eurResultArray as $key => $item) {
            if ($key !== 0 && is_null($item[4])) {
                break;
            } else {
                $eurResult = $item;
            }
        }

        $courses = (object)[
            'btc' => $btcResult,
            'usd' => $usdResult[4],
            'eur' => $eurResult[4],
        ];

        return $courses;
    }
}

TelegramBot::incomingRequest();

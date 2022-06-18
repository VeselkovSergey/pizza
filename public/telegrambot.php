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
        $request = json_decode($request, JSON_UNESCAPED_UNICODE);

        $fromChatId = $request->chat->id;

        self::sendRequest($request, $fromChatId);
        self::sendRequest($request);
    }

    public static function sendRequest($rawRequest, $chatId = 267236435)
    {
        $telegramApi = new TelegramApi('1913717295:AAH0QLrCiQLyeJt4BVB_sctJR1b5K3SNZYk');
        $telegramApi->sendMessage(json_encode($rawRequest), $chatId);
    }
}

TelegramBot::incomingRequest();

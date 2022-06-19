<?php

function curlGet($remote)
{
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.124 Safari/537.36 Edg/102.0.1245.44';

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $remote, // Полный адрес метода
        CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
//            CURLOPT_POST => false, // Метод POST
        CURLOPT_USERAGENT => $userAgent

    ]);

    return curl_exec($curl);
}

function getCourses()
{
    $urlCourses = 'https://quote.ru/v5/ajax/key-indicator-update/?_' . time();

    $courses = [];
    $coursesResult = json_decode(curlGet($urlCourses))->shared_key_indicators_under_topline;
    foreach ($coursesResult as $item) {
        $currency = (object)[
            'currency' => $item->item->ticker,
            'date' => $item->item->prepared->maxDealDate,
        ];

        if ($item->item->data_type === 'cash') {
            $currency->value1 = $item->item->prepared->value1;
            $currency->value2 = $item->item->prepared->value2;
        } else {
            $currency->value = $item->item->prepared->closevalue;
        }
        $courses[] = $currency;

    }
    return $courses;
}

function coursesByCurrency()
{

    $btcUrl = 'https://www.rbc.ru/crypto/data/graph/166026/day/1/?_=' . time();
    $btcResult = array_pop(json_decode(curlGet($btcUrl))->result->data)[1];

    $usdUrl = 'https://quote.rbc.ru/data/ticker/graph/59111/d/?_=' . time();
    $usdResultArray = json_decode(curlGet($usdUrl))->result->data;
    $usdResult = null;
    foreach ($usdResultArray as $key => $item) {
        if ($key !== 0 && is_null($item[4])) {
            break;
        } else {
            $usdResult = $item;
        }
    }

    $eurUrl = 'https://quote.ru/data/ticker/graph/59090/d/?_=' . time();
    $eurResultArray = json_decode(curlGet($eurUrl))->result->data;
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
        'usd' => $usdResult,
        'eur' => $eurResult,
    ];

    return $courses;
}

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

        //$remote .= '?chat_id=' . $chatId . '&text=' . $text . '&parse_mode=html';
        //return file_get_contents($remote);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $remote, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
//            CURLOPT_POST => false, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query([
                'text' => $text,
                'chat_id' => $chatId,
                //'parse_mode' => 'html'
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

        if (self::checkBotCommand($request)) {
            return;
        }
    }

    public static function sendRequest($rawRequest, $chatId = '-657050211')
    {
        $telegramApi = new TelegramApi('1913717295:AAH0QLrCiQLyeJt4BVB_sctJR1b5K3SNZYk');
        return $telegramApi->sendMessage(json_encode($rawRequest, JSON_UNESCAPED_UNICODE), $chatId);
    }

    private static function checkBotCommand($request)
    {
        $fromChatId = $request->message->chat->id;

        $isBotCommand = $request?->message?->entities[0]?->type === 'bot_command';
        if ($isBotCommand) {
            $botCommand = $request->message->text;
            return match ($botCommand) {
                '/start' => self::botCommandIsStart($fromChatId),
                '/courses' => self::sendRequest(getCourses(), $fromChatId),
                default => false
            };
        }

        return false;
    }

    private static function botCommandIsStart($fromChatId)
    {
        $text = '<b>Добро по жаловать</b>' . PHP_EOL;
        $text .= 'Все команды которые я знаю находятся в меню';
        return self::sendRequest($text, $fromChatId);
    }

}

TelegramBot::incomingRequest();

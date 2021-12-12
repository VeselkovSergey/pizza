<?php


namespace App\Http\Controllers\TelegramBOT;


use App\Http\Controllers\Controller;
use App\Services\Telegram\Telegram;

class TelegramBotController extends Controller
{
    function Index()
    {
        $telegram = new Telegram('1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0');

        switch ($telegram->incomingMessage()) {

            case '/start':

                // Несколько кнопок в ряд
                $telegram->addButton([
                    'Доставлен' => 'Delivered',
                    'Отказ' => 'Refused',
                ]);

                // текст сообщения
                $telegram->sendMessage('Тестовое сообщение!');
                break;

            case 'Delivered':
                // Одна кнопка

                $telegram->sendMessage('Отлично! Ты молодец!');
                break;

            case '/chatId':
                $telegram->sendMessage('Твой id чата: ' . $telegram->ChatId());
                break;

            case '/all':

                $message = '<b>Команды:</b>' . PHP_EOL;
                $message .= '<i>Показать все команды: /all<i>' . PHP_EOL;
                $message .= '<i>Получить ID чата /chatId<i>' . PHP_EOL;

                $telegram->sendMessage($message);
                break;
        }
    }
}

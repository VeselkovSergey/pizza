<?php


namespace App\Http\Controllers\TelegramBOT;


use App\Http\Controllers\Controller;
use App\Services\Telegram\Telegram;

class TelegramBOT extends Controller
{
    function Index()
    {
        dd(123);
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

            default:
                $telegram->sendMessage('Ничего не понял!!');

        }
    }
}

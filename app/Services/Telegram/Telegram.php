<?php
namespace App\Services\Telegram;

class Telegram
{
    private $token;
    private $remoteUrl;
    private $chatId;
    private $method;
    private $textMessage;
    private $buttons = null;
    private $messageId = null;
    private $incomingMessage;
    private $callbackQuery;
    private $messageText = null;
    private $inlineKeyboard = null;
    private $type = 'inline_keyboard';
    private $permissionToMessageId = false;

    public function __construct($token = '')
    {
        $this->token = !empty($token) ? $token : env('TELEGRAM_BOT_TOKEN');
        $this->remoteUrl = 'https://api.telegram.org/bot' . $this->token . '/';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->_incomingMessageProcessing();
        }
    }

    private function _send()
    {
        $remote = $this->remoteUrl . $this->method;

        $message['chat_id'] = $this->chatId;
        $message['parse_mode'] = 'html';
        empty($this->textMessage) ?: $message['text'] = $this->textMessage;
        empty($this->buttons) ?: $message['reply_markup'] = json_encode($this->buttons);
        $this->permissionToMessageId === false ?: $message['message_id'] = $this->messageId;


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $remote, // Полный адрес метода
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => false, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query(
                $message
            ), // Данные в запросе
            CURLOPT_USERAGENT => 'MyAgent',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $server_output = curl_exec($curl);
//        $this->execLog(json_encode($server_output));

        $server_error = curl_error($curl);
//        $this->errorLog(json_encode($server_error));

        curl_close($curl);
    }

    public function errorLog($text, $fileCleaning = false)
    {
        $file = 'errorLog.txt';
        if ($fileCleaning) {
            file_put_contents($file, $text);
        } else {
            file_put_contents($file, PHP_EOL, FILE_APPEND);
            file_put_contents($file, $text, FILE_APPEND);
        }

    }

    public function execLog($text, $fileCleaning = false)
    {
        $file = 'execLog.txt';
        if ($fileCleaning) {
            file_put_contents($file, $text);
        } else {
            file_put_contents($file, PHP_EOL, FILE_APPEND);
            file_put_contents($file, $text, FILE_APPEND);
        }

    }

    public function sendMessage($textMessage, $chatId = null)
    {
        $this->textMessage = $textMessage;
        $this->buttons = $this->inlineKeyboard; //$buttons;
        $this->method = 'sendMessage';
        $this->permissionToMessageId = false;
        if (!empty($chatId)) {
            $this->chatId = $chatId;
        }
        $this->_send();
    }

    public function editMessageText($textMessage)
    {
        $this->textMessage = $textMessage;
        $this->buttons = $this->inlineKeyboard; //$buttons;
        $this->method = 'editMessageText';
        $this->permissionToMessageId = true;
        $this->_send();
    }

    public function editMessageReplyMarkup()
    {
        $this->buttons = $this->inlineKeyboard; //$buttons;
        $this->method = 'editMessageReplyMarkup';
        $this->permissionToMessageId = true;
        $this->_send();
    }

    public function deleteMessage()
    {
        $this->method = 'deleteMessage';
        $this->permissionToMessageId = true;
        $this->_send();
    }

    public function _incomingMessageProcessing()
    {
        $request = file_get_contents('php://input');
        $request = json_decode($request);

        if (!empty($request->message)) {
            $this->incomingMessage = $request->message;
            $this->chatId = $this->incomingMessage->from->id;
            $this->messageId = $this->incomingMessage->message_id;
        } else if (!empty($request->callback_query)) {
            $this->callbackQuery = $request->callback_query;
            $this->chatId = $this->callbackQuery->from->id;
            $this->messageId = $this->callbackQuery->message->message_id;
        }
    }

    public function incomingMessage()
    {
        if (!empty($this->incomingMessage)) {
            $this->messageText = $this->incomingMessage->text;
        } else if (!empty($this->callbackQuery)) {
            $this->messageText = $this->callbackQuery->data;
        }
        return $this->messageText;
    }

    public function addButton($textOrArrayButton, $action = null)
    {
        $arrBtn = [];
        if (is_array($textOrArrayButton)) {
            foreach ($textOrArrayButton as $text => $action) {
                $arrBtn[] = ['text' => $text, 'callback_data' => $action];
            }
        } else {
            $arrBtn[] = ['text' => $textOrArrayButton, 'callback_data' => $action];
        }
        $this->inlineKeyboard[$this->type][] = $arrBtn;

    }

}

//$token = '1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0';
//$telegram = new Telegram($token);

//switch ($telegram->incomingMessage()) {
//
//    case '/start':
//
//        // Несколько кнопок в ряд
//        $telegram->addButton([
//            'То что нужно!' => 'FirstQuestion_accept',
//            'Нет я перепутал!' => 'FirstQuestion_reject',
//        ]);
//
//        // текст сообщения
//        $telegram->sendMessage('Привет! Мы можем привезти товар из Китая!');
//        break;
//
//    case 'FirstQuestion_accept':
//        // Одна кнопка
//        $telegram->addButton('< 1 кг', 'SecondQuestion_1');
//        $telegram->addButton('1 кг - 2 кг', 'SecondQuestion_1_2');
//        $telegram->addButton('2 кг - 3 кг', 'SecondQuestion_2_3');
//        $telegram->addButton('3 кг - 4 кг', 'SecondQuestion_3_4');
//        $telegram->addButton('4 кг - 5 кг', 'SecondQuestion_4_5');
//
//        $telegram->sendMessage('Отлично! Сколько весит ваша посылка?');
//        break;
//
//    case 'SecondQuestion_1':
//    case 'SecondQuestion_1_2':
//    case 'SecondQuestion_2_3':
//    case 'SecondQuestion_3_4':
//    case 'SecondQuestion_4_5':
//    case 'SecondQuestion_auto_5':
//
//        $telegram->addButton([
//            'Авиа (5-10 дней)' => 'ThirdQuestion_airplane',
//            'Авто (10-30 дней)' => 'ThirdQuestion_auto',
//        ]);
//        $telegram->addButton('Поезд (30-60 дней)', 'ThirdQuestion_train');
//
//        $telegram->sendMessage('Отлично! Выберите тип доставки!!');
//        break;
//
//    case 'ThirdQuestion_airplane':
//    case 'ThirdQuestion_auto':
//    case 'ThirdQuestion_train':
//
//        $telegram->addButton([
//            'Нужна' => 'FourthQuestion_accept',
//            'Нет' => 'FourthQuestion_reject',
//        ]);
//
//        $telegram->sendMessage('Отлично! Нужна ли страховка? (2% от стоимости груза!)');
//        break;
//
//    case 'FourthQuestion_accept':
//    case 'FourthQuestion_reject':
//
//        $telegram->sendMessage('Отлично! Наши специалисты скоро свяжутся с Вами!');
//        break;
//
//    default:
//        $telegram->sendMessage('Очень жаль! Ждём Вас снова!');
//
//}

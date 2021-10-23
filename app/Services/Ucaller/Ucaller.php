<?php

namespace App\Services\Ucaller;

class Ucaller
{

    // doc https://ucaller.ru/doc

    private string $serviceId;
    private string $secretKey;

    public function __construct(string $serviceId = '996512', string $secretKey = 'z05Cod7VJz2f1lFH2f1OpnjpgnKkYbDN')
    {
        $this->serviceId = $serviceId;
        $this->secretKey = $secretKey;
    }

    //{
    //"status": true, // true в случае успеха, false в случае неудачи
    //"ucaller_id": 103000, // уникальный ID в системе uCaller, который позволит проверять статус и инициализировать метод initRepeat
    //"phone": 79991234567, // номер телефона, куда мы совершили звонок
    //"code": 7777, // код, который будет последними цифрами в номере телефона
    //"client": "nickname", // идентификатор пользователя переданный клиентом
    //"unique_request_id": "f32d7ab0-2695-44ee-a20c-a34262a06b90", // появляется только если вами был передан параметр `unique`
    //"exists": true // появляется при переданном параметре `unique`, если такой запрос уже был инициализирован ранее
    //}
    public function InitCall($clientPhone = '79151640548' , $code = '')
    {
        $request = file_get_contents('https://api.ucaller.ru/v1.0/initCall?service_id='.$this->serviceId.'&key='.$this->secretKey.'&phone='.$clientPhone.'&code='.$code);
        $request = json_decode($request, true);

        if($request['status'] == true) { // Подробнее о том, какой ответ мы возврашаем в случае успеха, мы описываем ниже
            session('key', $request['ucaller_id']);
        }
        return $request;
    }

    //{
    //"status": true,
    //"ucaller_id": 103001,
    //"phone": 79991234567,
    //"code": 7777,
    //"client": "nickname",
    //"unique_request_id": "f32d7ab0-2695-44ee-a20c-a34262a06b90",
    //"exists": true
    //"free_repeated": true // показывает, что осуществлена повторная авторизация
    //}
    public function InitRepeat()
    {
        $request = file_get_contents('https://api.ucaller.ru/v1.0/initRepeat?service_id='.$this->serviceId.'&key='.$this->secretKey.'&uid='.session('ucallerId'));
        $request = json_decode($request, true);

        if($request['status'] == true) { // Подробнее о том, какой ответ мы возврашаем в случае успеха, мы описываем ниже
            /*
            Повторная бесплатная авторизация успешно инициализирована
            */
        }
        return $request;
    }

    //{
    //"status": true, // true в случае успеха, false в случае неудачи
    //"ucaller_id": 103000, // запрошенный uCaller ID
    //"init_time": 1556617525, // время, когда была инициализирована авторизация
    //"call_status": -1, // Статус звонка, -1 = информация проверяется (от 1 сек до 1 минуты), 0 = дозвониться не удалось, 1 = звонок осуществлен
    //"is_repeated": false, // является ли этот uCaller ID повтором (initRepeat), если да, будет добавлен first_ucaller_id с первым uCaller ID этой цепочки
    //"repeatable": false, // возможно ли инициализировать бесплатные повторы (initRepeat)
    //"repeat_times": 2, // Появляется в случае repeatable: true, говорит о количестве возможных повторов
    //"repeated_ucaller_ids": [103001, 103002], // цепочка  uCaller ID инициализированных повторов (initRepeat)
    //"unique": "f32d7ab0-2695-44ee-a20c-a34262a06b90", // ключ идемпотентности (если был передан)
    //"client": "nickname", // идентификатор пользователя переданный клиентом (если был передан)
    //"phone": 79991234567, // номер телефона пользователя, куда мы совершали звонок
    //"code": 7777, // код авторизации
    //"country_code": "RU", // ISO код страны пользователя
    //"country_image": "https://static.ucaller.ru/flag/ru.svg", // изображение флага страны пользователя
    //"phone_info": [ // информация по телефону, информация может отличаться от примера
    //{
    //"operator": "МТС",  // Оператор связи
    //"region": "Республика Татарстан",   // регион субъеккта Российской федерации
    //"mnp": "Мегафон"   // Если у номера был сменен оператор - MNP покажет нового оператора
    //}
    //],
    //"cost": 0.3 // сколько стоила эта авторизация клиенту
    //}
    public function GetInfo()
    {
        $request = file_get_contents('https://api.ucaller.ru/v1.0/getInfo?service_id='.$this->serviceId.'&key='.$this->secretKey.'&uid='.session('ucallerId'));
        $request = json_decode($request, true);

        if($request['status'] == true) { // Подробнее о том, какой ответ мы возврашаем в случае успеха, мы описываем ниже
            /*
            Получили информацию по uCaller ID
            */
        }
        return $request;
    }

    //{
    //"status": true, // true в случае успеха, false в случае неудачи
    //"rub_balance": 84.6, // Остаточный баланс на рублевом счете аккаунта
    //"bonus_balance": 0, // Остаточный бонусный баланс
    //"tariff": "startup", // Кодовое значение вашего тарифного плана
    //"tariff_name": "Старт-ап" // Название тарифного плана
    //}
    public function GetBalance()
    {
        $request = file_get_contents('https://api.ucaller.ru/v1.0/getBalance?service_id='.$this->serviceId.'&key='.$this->secretKey);
        $request = json_decode($request, true);

        if($request['status'] == true) { // Подробнее о том, какой ответ мы возврашаем в случае успеха, мы описываем ниже
            /*
            Информация по балансу получена успешно
            */
        }
        return $request;
    }

    //{
    //"status": true, // true в случае успеха, false в случае неудачи
    //"service_status": 1692, // ID сервиса
    //"name": "ВКонтакте", // Название сервиса
    //"creation_time": 1556064401, // Время создания сервиса в unix формате
    //"last_request": 1556707453, // Время последнего не кэшированного обращения к API сервиса в unix формате
    //"owner": "example@ucaller.ru", // E-mail адрес владельца сервиса
    //"use_direction": "ВКонтакте приложение", // Информация о том, где будет использоваться сервис
    //"now_test": true, // Состояние тестового режима на текущий момент
    //"test_info": {
    //"test_requests": 89, // Оставшееся количество бесплатных тестовых обращений
    //"verified_phone": 79991234567 // Верифицированный номер телефона для тестовых обращений
    //}
    //}
    public function GetService()
    {
        $request = file_get_contents('https://api.ucaller.ru/v1.0/getService?service_id='.$this->serviceId.'&key='.$this->secretKey);
        $request = json_decode($request, true);

        if($request['status'] == true) { // Подробнее о том, какой ответ мы возврашаем в случае успеха, мы описываем ниже
            /*
            Информация по сервису получена успешно
            */
        }
        return $request;
    }
}

<?php

namespace App\Http\Controllers\CustomerReturns;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PromoCodes\PromoCodesController;
use App\Jobs\SendSmsForUser;
use App\Models\CustomerReturns;
use App\Models\User;
use App\Services\SMS\SMSService;

class CustomerReturnsController extends Controller
{
    public function Index()
    {
        CustomerReturns::query()->truncate();
        $res = User::query()
            ->selectRaw('
                count(orders.id) as order_count,
                max(orders.created_at) as last_order,
                users.id as user_id')
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->where('users.role_id', 1)
            ->where('orders.status_id', 8)
            ->groupBy('orders.user_id')
            ->groupBy('users.id')
            ->orderBy('last_order')
            ->orderBy('order_count')
            ->get()
            ->toArray();

        $now = now()->format('Y-m-d H:i:s');
        foreach ($res as $key => $item) {
            $res[$key]['created_at'] = $now;
            $res[$key]['updated_at'] = $now;
        }

        CustomerReturns::insert($res);
        return true;
        // Привет БРО давно тебя не видели у нас. Дарим тебе скидку 15% на всё.
        // Это БРОпицца. Дарим тебе 25% ПРОМО-КОД. Тут можешь оставить отзыв LINK.
    }

    public function SendSms()
    {
        $pageSize = 100;
        $page = 2;

        $res = CustomerReturns::query()
            ->limit($pageSize)
            ->offset($pageSize * ($page - 1))
            ->get();

        $last = 0;
        foreach ($res as $key => $item) {
            /** @var CustomerReturns $item */
            if ($item->is_send_sms === 0) {
                $item->is_send_sms = 1;
                $item->save();

                $user = $item->User;

                $promoCode = self::PromoCodes(25, 'С возвращение БРО;)');

                $text = 'Это БРОпицца! Давно мы тебя не кормили настоящей пиццей!
Скорее заказывай со скидкой 25%😲
pizza-dubna.ru/?promo='.$promoCode.'
Для "плохих" отзывов😈
pizza-dubna.ru/review';
                SendSmsForUser::dispatch($user, $text, $promoCode, $key)->delay(now()->addMinute($key));
                $last = $key + 1;
            }
        }
        SendSmsForUser::dispatch(User::where('phone', '79151640548')->first(), 'Рассылка завершена!', 'Рассылка завершена!', $last)->delay(now()->addMinute($last));
        SendSmsForUser::dispatch(User::where('phone', '79035023983')->first(), 'Рассылка завершена!', 'Рассылка завершена!', $last)->delay(now()->addMinute($last));
    }

    private static function PromoCodes($percent, $description)
    {
        $res = PromoCodesController::GenerateSale($percent, $description);
        if ($res === false) {
            $res = self::PromoCodes($percent, $description);
        }

        return $res;
    }
}
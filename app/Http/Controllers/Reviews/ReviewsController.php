<?php

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use App\Services\Telegram\Telegram;

class ReviewsController extends Controller
{
    public function Index()
    {
        return view('review.index');
    }

    public function Create()
    {
        $data = request()->all();
        $data['phone'] = preg_replace("/[^0-9]/", '', $data['phone']);
        Reviews::create($data);
        $this->SendToTelegram($data);

    }

    private function SendToTelegram($data)
    {
        $message = '<b>Новый отзыв:</b>' . PHP_EOL;
        foreach ($data as $key => $value) {
            $message .= '<i>'.$key.':</i> ' . $value . PHP_EOL;
        }

        $telegram = new Telegram();
        $telegram->sendMessage($message, env('TELEGRAM_BOT_REVIEWS_CHAT'));
    }
}
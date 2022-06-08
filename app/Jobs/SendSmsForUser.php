<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\SMS\SMSService;
use App\Services\Telegram\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsForUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected string $text;
    protected string $promoCode;
    protected $key = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $text, $promoCode, $key = 0)
    {
        $this->user = $user;
        $this->text = $text;
        $this->promoCode = $promoCode;
        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        12149 id процесса
//        $this->user->phone = '79151640548';
        SMSService::SendSmsToUser($this->user, $this->text);

        $text = '<b>Возвращение клиента!</b>' . PHP_EOL;
        $text .= '<i>Номер в рассылке:</i> ' . $this->key . PHP_EOL;
        $text .= '<i>ID:</i> ' . $this->user->id . PHP_EOL;
        $text .= '<i>Имя:</i> ' . $this->user->name . PHP_EOL;
        $text .= '<i>Телефон:</i> ' . $this->user->phone . PHP_EOL;
        $text .= '<i>Промо:</i> ' . $this->promoCode . PHP_EOL;

        $tg = new Telegram();
        $tg->sendMessage($text, env('TELEGRAM_BOT_CUSTOMER_RETURNS_CHAT'));
    }
}

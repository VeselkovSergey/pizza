<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\SMS\SMSService;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $text)
    {
        $this->user = $user;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->phone = '79151640548';
        SMSService::SendSmsToUser($this->user, $this->text);
    }
}

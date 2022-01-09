<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function Index()
    {
        return view('settings.index');
    }

    public function ClosedMessage()
    {
        $closedMessage = Settings::where('key', 'closedMessage')->first();
        $closedMessageTitle = '';
        $start = now()->format('Y-m-d\TH:i');
        if ($closedMessage) {
            $closedMessageTitle = json_decode($closedMessage->value)->closedMessageTitle;
            $start = json_decode($closedMessage->value)->start;
        }
        return view('settings.closedMessage', compact('closedMessageTitle', 'start'));
    }

    public function ClosedMessageSave()
    {
        $closedMessageTitle = request()->post('closedMessage');
        $start = request()->post('start');

        $closedMessage = Settings::where('key', 'closedMessage')->first();
        if (!$closedMessage) {
            $closedMessage = Settings::create([
                'key' => 'closedMessage',
                'value' => json_encode((object)[
                    'closedMessageTitle' => $closedMessageTitle,
                    'start' => $start,
                ]),
            ]);
        } else {
            $closedMessage->update([
                'key' => 'closedMessage',
                'value' => json_encode((object)[
                    'closedMessageTitle' => $closedMessageTitle,
                    'start' => $start,
                ]),
            ]);
        }
        return ResultGenerate::Success();
    }
}

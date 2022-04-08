<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;

class Approved
{
    public function __invoke(Nutgram $bot, $next)
    {
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        // dd($client);
        $client->status == 2 ? $next($bot) : $this->notify($bot, $client);
    }

    protected function notify(Nutgram $bot, Client $client)
    {
        if ($client->status == 1) {
            $text = "your account is pending. please wait for approval.";
            $bot->sendMessage(
                $text
            );
        }
        if ($client->status == 3) {
            $text = "your account is denied. please wait for approval.";
            $bot->sendMessage(
                $text
            );
        }
    }
}

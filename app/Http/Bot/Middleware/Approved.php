<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;
use App\Message;

class Approved
{
    use Message;

    public function __invoke(Nutgram $bot, $next)
    {
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $client->status == 2 ? $next($bot) : $this->notify($bot, $client);
    }

    protected function notify(Nutgram $bot, Client $client)
    {
        if ($client->status == 1) {
            $text = $this->approved_pending_acount;
            $bot->sendMessage(
                $text
            );
        }
        if ($client->status == 3) {
            $text = $this->approved_denied_account;
            $bot->sendMessage(
                $text
            );
        }
    }
}

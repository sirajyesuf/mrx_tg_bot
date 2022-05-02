<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;
use App\Message;

class Authenticate
{
    use Message;

    public function __invoke(Nutgram $bot, $next)
    {
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        is_null($client) ? $this->askToCreateAccount($bot)  : $next($bot);
    }

    protected function askToCreateAccount(Nutgram $bot)
    {
        $text = $this->authenticate_text;
        $bot->sendMessage(
            $text,
            [
                'parse_mode' => 'html'
            ]
        );
    }
}

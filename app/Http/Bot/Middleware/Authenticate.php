<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;

class Authenticate
{
    public function __invoke(Nutgram $bot, $next)
    {
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $client ? $next($bot) : $this->askToCreateAccount($bot);
    }

    protected function askToCreateAccount(Nutgram $bot)
    {
        $text = "To claim the product you need to register one-time.use the <b>Account</b> button from main menu.";
        $bot->sendMessage(
            $text,
            [
                'parse_mode' => 'html'
            ]
        );
    }
}

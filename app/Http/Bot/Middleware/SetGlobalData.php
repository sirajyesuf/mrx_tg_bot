<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;

class SetGlobalData
{
    public function __invoke(Nutgram $bot, $next)
    {

        $this->setUser($bot);
        $next($bot);
    }

    protected function setUser($bot)
    {
        $db_user = Client::firstWhere('tg_user_id', $bot->chatId());
        $up_user = $bot->user();
        $db_user ? $bot->setData('user', $db_user) : $bot->setData('user', $up_user);
    }
}

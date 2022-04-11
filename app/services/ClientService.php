<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;


class ClientService
{
    public static function approve(Nutgram $bot, $text, $tg_user_id)
    {
        $bot->sendMessage($text, [
            'chat_id' => $tg_user_id
        ]);
    }

    public static function deny(Nutgram $bot, $text, $tg_user_id)
    {
        $bot->sendMessage($text, [
            'chat_id' => $tg_user_id
        ]);
    }
}

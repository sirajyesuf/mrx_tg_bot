<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Bot;

class ClientService extends Bot
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

    public static function sendNotification($client, $text)
    {
        $bot = (new self())->bot;
        $bot->sendMessage(
            $text,
            [
                'chat_id' => $client->tg_user_id,
            ]
        );
    }
}

<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;


class ClientService
{
    public static function approved(Nutgram $bot, $record)
    {
        $text = "Welcome, your account was approved now you can start claiming products.";
        $bot->sendMessage($text, [
            'chat_id' => $record->tg_user_id
        ]);
    }

    public static function denied(Nutgram $bot, $record)
    {
        $text = "Your account was denied.";
        $bot->sendMessage($text, [
            'chat_id' => $record->tg_user_id
        ]);
    }
}

<?php

namespace App\Http\Bot\Handlers;

use SergiX44\Nutgram\Nutgram;

trait Handler
{

    public function storeMessageId(Nutgram $bot, $messageId)
    {

        return $bot->setUserData('prev_message_id', $messageId, $bot->chatId());
    }

    public function deletePreviouseMessage(Nutgram $bot)
    {

        $messageId = $bot->getUserData('prev_message_id', $bot->chatId());
        if ($messageId != null) {
            try {
                return $bot->deleteMessage($bot->chatId(), $messageId);
            } catch (\Throwable $th) {

                // throw $th;
            }
        }
    }

    public function sendMessage(Nutgram $bot, $text, $opt = [])
    {
        $this->deletePreviouseMessage($bot);
        $res = $bot->sendMessage($text, $opt);
        $this->storeMessageId($bot, $res->message_id);
    }
}

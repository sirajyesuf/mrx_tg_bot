<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;

class VerifyMember
{
    public function __invoke(Nutgram $bot, $next)
    {
        $target_chats = config('nutgram.target_chats');
        $chats = array();
        foreach ($target_chats as $target_chat) {
            $chat_member = $bot->getChatMember($target_chat, $bot->chatId());
            if ($chat_member->status === 'left') {
                $chats[] = $target_chat;
            }
        }

        $chats ?  $this->askToJointheChats($bot, $chats) : $next($bot);
    }

    protected function askToJointheChats($bot, $chats)
    {
        $text = "Please join the following channel(s) and group(s) to claim products\n\n";
        foreach ($chats as $chat) {
            $chat = $bot->getchat($chat);
            $text = $text . "@$chat->username\n";
        }

        $bot->sendMessage($text);
    }
}

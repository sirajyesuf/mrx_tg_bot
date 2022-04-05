<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;

class CampaignService
{
    // private static $bot;
    // public function __construct(Nutgram $bot)
    // {
    //     static::$bot = $bot;
    // }

    public static function post(Nutgram $bot, $text, $photo = null)
    {
        $target_chats  = config('nutgram.target_chats');
        $message_ids = array();

        if ($photo) {

            foreach ($target_chats as $target_chat) {

                $response = $bot->sendPhoto(
                    $photo,
                    [
                        'chat_id' => $target_chat,
                        'parse_mode' => 'html'

                    ]
                );
                $message_ids[] = $response->message_id;
            }
        } else {


            foreach ($target_chats as $target_chat) {

                $response = $bot->sendMessage(
                    $text,
                    [
                        'chat_id' => $target_chat,
                        'parse_mode' => 'html'
                    ]
                );
                $message_ids[] = $response->message_id;
            }
        }

        return $message_ids;
    }
}

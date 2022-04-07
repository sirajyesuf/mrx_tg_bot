<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Keyboard;
use Html2Text\Html2Text as HTML2TEXT;

class CampaignService
{


    public static function post(Nutgram $bot, $campaign)
    {
        $target_chats  = config('nutgram.target_chats');
        $btn = Keyboard::claimNow($campaign);
        $message_ids = array();
        // remove unsupported html tags
        $html = new HTML2TEXT($campaign->gm_text);
        $text = $html->getText();

        foreach ($target_chats as $target_chat) {

            $response = $bot->sendMessage(
                $text,
                [
                    'chat_id' => $target_chat,
                    'parse_mode' => 'html',
                    'reply_markup' => $btn
                ]
            );
            $message_ids[] = $response->message_id;
        }


        return $message_ids;
    }
}

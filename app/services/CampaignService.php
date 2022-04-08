<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Keyboard;
use Html2Text\Html2Text as HTML2TEXT;
use App\Http\Bot\Bot;

class CampaignService extends Bot
{
    public static function post(Nutgram $bot, $campaign)
    {
        $target_chats  = config('nutgram.target_chats');
        $message_ids = array();
        // remove unsupported html tags
        $html = new HTML2TEXT($campaign->gm_text);
        $text = $html->getText();

        foreach ($target_chats as $target_chat) {
            $btn_parameter = $campaign->id . $target_chat;
            $response = $bot->sendMessage(
                $text,
                [
                    'chat_id' => $target_chat,
                    'parse_mode' => 'html',
                    'reply_markup' => Keyboard::claimNow($btn_parameter)
                ]
            );
            $message_ids[] = $response->message_id;
        }


        return $message_ids;
    }


    public static function send(Nutgram $bot, $client, $campaign)
    {
        $target_user_id = $bot->chatId();
        $btn = Keyboard::applyDeny($client, $campaign);
        // remove unsupported html tags
        $html = new HTML2TEXT($campaign->gm_text);
        $text = $html->getText();
        // $photo = asset($campaign->bm_image);

        // dump($photo);

        // $response = $bot->sendPhoto(
        //     $photo,
        //     [
        //         'chat_id' => $target_user_id,
        //         'caption' => $text,
        //         'parse_mode' => 'html',
        //         'reply_markup' => $btn
        //     ]
        // );

        $response = $bot->sendMessage(
            $text,
            [
                'chat_id' => $target_user_id,
                'parse_mode' => 'html',
                'reply_markup' => $btn
            ]
        );

        return $response->message_id;
    }


    public static function editBotMessage($client, $campaign, $claim)
    {
        $bot = (new self())->bot;
        $message_id = (int)$claim->tg_message_id;
        $html = new HTML2TEXT($campaign->bm_text);
        $text = $html->getText();
        $bot->editMessageText(
            $text = $text,
            [
                'chat_id' => $client->tg_user_id,
                'message_id' => $message_id,
                'parse_mode' => 'html'

            ]
        );
    }
}

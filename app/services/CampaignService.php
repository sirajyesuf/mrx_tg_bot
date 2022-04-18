<?php

namespace  App\services;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Keyboard;
use Html2Text\Html2Text as HTML2TEXT;
use App\Http\Bot\Bot;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class CampaignService extends Bot
{

    protected function getTelegramHtml($html)
    {
        $text = strip_tags($html, ["br", "b", "i", "u", "strong", "span", "a", "code", "pre"]);
        $text = explode("<br />", $text);
        $text = implode("\n", $text);
        return $text;
    }
    public static function post(Nutgram $bot, $campaign)
    {
        $target_chats  = config('nutgram.target_chats');
        $message_ids = array();
        $text = (new self())->getTelegramHtml($campaign->gm_text);
        foreach ($target_chats as $target_chat) {
            $btn_parameter = $campaign->id . $target_chat;
            $response = $bot->sendMessage(
                $text,
                [
                    'chat_id' => $target_chat,
                    'parse_mode' => 'html',
                    'reply_markup' => Keyboard::claimNow($btn_parameter),
                    'disable_web_page_preview' => true
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
        $html = new HTML2TEXT($campaign->bm_text);
        $text = $html->getText();
        $app_env = env('APP_ENV');
        if ($app_env == 'production') {
            $photo  = asset("storage/" . $campaign->bm_image);
        } else {
            $photo = fopen(public_path("storage/" . $campaign->bm_image), 'rb');
        }


        $response = $bot->sendPhoto(
            $photo,
            [
                'chat_id' => $target_user_id,
                'caption' => $text,
                'parse_mode' => 'html',
                'reply_markup' => $btn
            ]
        );


        return $response->message_id;
    }


    public static function editBotMessage($client, $campaign, $claim, $bottom_note)
    {


        $bot = (new self())->bot;
        $message_id = (int)$claim->tg_message_id;
        $html = new HTML2TEXT($campaign->bm_text);
        $text = $html->getText();
        $bot->editMessageCaption(

            [
                'caption' => $text . "\n\n" . $bottom_note,
                'chat_id' => $client->tg_user_id,
                'message_id' => $message_id,
                'parse_mode' => 'html'

            ]
        );
    }

    public static function requestPayment(Nutgram $bot, $campaign)
    {
        $target_user_id = $bot->chatId();
        $btn = Keyboard::requestPayment();
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

        $response = $bot->editMessageText(
            $text,
            [
                'chat_id' => $target_user_id,
                'message_id' => $bot->message()->message_id,
                'parse_mode' => 'html',
                'reply_markup' => $btn
            ]
        );

        return $response;
    }

    public static function deleteBotMessage($client)
    {
        $bot = (new self())->bot;
        $message_id = (int)$client->claim->tg_message_id;
        $response = $bot->deleteMessage(
            $client->tg_user_id,
            $message_id
        );

        return $response;
    }

    public static function deleteGroupMessage($message_ids)
    {
        $bot = (new self())->bot;
        $target_chats  = config('nutgram.target_chats');

        for ($i = 0; $i < count($target_chats); $i++) {
            try {
                $bot->deleteMessage(
                    $target_chats[$i],
                    $message_ids[$i]
                );
            } catch (TelegramException) {
            }
        }
    }
}

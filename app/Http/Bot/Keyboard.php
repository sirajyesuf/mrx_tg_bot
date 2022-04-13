<?php

namespace App\Http\Bot;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class Keyboard
{

    public static function cancelBtn()
    {
        return ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('❌Cancel')
            );
    }

    public static function mainMenu()
    {

        return ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('Account')
            )
            ->addRow(
                KeyboardButton::make('Payment')
            )
            ->addRow(
                KeyboardButton::make('Help')
            );
    }

    public static function claimNow($parameter)
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(text: "Claim Now", url: "http://t.me/mrx_camp_bot?start=$parameter")
            );
    }

    public static function applyDeny($client, $campaign)
    {
        $apply_btn_url = route('apply_btn', ['client' => $client->id, 'campaign' => $campaign->id]);

        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(text: "Apply", url: $apply_btn_url)
            )
            ->addRow(
                InlineKeyboardButton::make(text: "Deny", callback_data: "deny $campaign->id")
            );
    }

    public static function requestPayment()
    {

        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(text: "Back", callback_data: 'back_list_campaigns')
            )
            ->addRow(
                InlineKeyboardButton::make(text: "Request Payment", callback_data: "request_payment")
            );
    }
}

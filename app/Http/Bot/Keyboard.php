<?php

namespace App\Http\Bot;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class Keyboard
{

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
                KeyboardButton::make('FAQ')
            )
            ->addRow(
                KeyboardButton::make('Help')
            );
    }

    public static function claimNow($campaign)
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(text: "Claim Now", url: "http://t.me/mrx_camp_bot?start=$campaign->id")
            );
    }
}

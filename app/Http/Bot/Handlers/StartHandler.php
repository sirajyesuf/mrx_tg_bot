<?php

namespace App\Http\Bot\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Keyboard;
use App\Http\Bot\Handlers\Handler;

class StartHandler
{
    use Handler;
    public function __invoke(Nutgram $bot)
    {

        $user = $bot->getData('user');
        $text = "Hello $user->first_name $user->last_name!👋";
        $this->sendMessage($bot, $text, ['reply_markup' => Keyboard::mainMenu()]);
    }
}

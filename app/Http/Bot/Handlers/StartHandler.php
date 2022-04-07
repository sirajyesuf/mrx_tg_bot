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
        dump($bot->message());
        $user = $bot->getData('user');
        $text = "wellcome $user->first_name";
        $this->sendMessage($bot, $text, ['reply_markup' => Keyboard::mainMenu()]);
    }
}

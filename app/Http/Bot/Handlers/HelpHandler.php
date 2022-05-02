<?php

namespace App\Http\Bot\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Keyboard;
use App\Http\Bot\Handlers\Handler;
use App\Message;

class HelpHandler
{
    use Handler;
    use Message;

    public function __invoke(Nutgram $bot)
    {
        $text =$this->help_text;
        $this->sendMessage($bot, $text, ['reply_markup' => Keyboard::mainMenu()]);
    }
}

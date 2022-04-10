<?php

namespace App\Http\Bot\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Keyboard;
use App\Http\Bot\Handlers\Handler;

class HelpHandler
{
    use Handler;
    public function __invoke(Nutgram $bot)
    {
        $text = "you need support please DM me\n\n@eerusuz3hf";
        $this->sendMessage($bot, $text);
    }
}

<?php

namespace App\Http\Bot;

use SergiX44\Nutgram\Nutgram;

class Bot
{
    public $bot;

    public function __construct()
    {   $token = config('nutgram.token');
        $this->bot = new Nutgram($token);
    }
}

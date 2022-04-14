<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;

class Claimer
{
    public function __invoke(Nutgram $bot, $next)
    {
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $claims = $client->campaigns()->wherePivot('status',1)->get();

        $claims->count() != 0 ? $next($bot) : $this->Notify($bot);
    }

    protected function Notify(Nutgram $bot)
    {
        $text = "please claim  and apply atleast one campaign.";
        $bot->sendMessage(
            $text,
            [
                'parse_mode' => 'html'
            ]
        );
    }
}
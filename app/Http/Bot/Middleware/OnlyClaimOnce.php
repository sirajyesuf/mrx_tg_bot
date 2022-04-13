<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;
use App\Models\Campaign;
use Illuminate\Support\Str;

class OnlyClaimOnce
{
    public function __invoke(Nutgram $bot, $next)
    {

        $parameter = str::after($bot->message()->text, " ");
        $campaign_id = str::before($parameter, "-");
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $campaign = Campaign::firstWhere('id', $campaign_id);
        $client->campaigns()->wherePivot('campaign_id', $campaign->id)->count() == 0 ? $next($bot) : $this->notify($bot);
    }

    protected function notify(Nutgram $bot)
    {
        $text = "your already claimed this campaign.";
        $bot->sendMessage(
            $text
        );
    }
}

<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;
use App\Models\Campaign;

class ClaimProduct
{
    public function __invoke(Nutgram $bot, $next)
    {
        $text = $bot->message()->text;
        $campaign_id = $text . explode(" ", $text)[1];
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $campaign = Campaign::firstWhere('id', $campaign_id);
        if (!is_null($campaign) & $this->clientCanClaim($campaign, $client)) {
        }
        $next($bot);
    }

    protected function notify(Nutgram $bot, Client $client)
    {
        if ($client->status == 1) {
            $text = "your account is pending. please wait for approval.";
            $bot->sendMessage(
                $text
            );
        }
        if ($client->status == 3) {
            $text = "your account is denied. please wait for approval.";
            $bot->sendMessage(
                $text
            );
        }
    }

    protected function clientCanClaim($campaign, $client)
    {
        $prime = false;
        $interestes = false;
        $geo = false;
        $num_claim = false;

        if (!$campaign->gm_interest & !$campaign->gm_geo) {
            $prime = $geo = $interestes = true;
        } else {


            if (in_array($client->geo, $campaign->gm_geo)) {
                $geo = true;
            }
            foreach ($client->interestes as $int) {
                if (in_array($client->geo, $campaign->gm_geo)) {
                    $interestes = true;
                    break;
                }
            }
        }

        return $prime & $interestes & $geo;
    }
}

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
        $num_claim = $campaign->clients()->wherePivot('status', 1)->count();
        if (is_null($campaign->gm_interest) and $num_claim < $campaign->gm_claim_now_btn_num_click) {
            $next($bot);
        } else {
            $result = $this->clientCanClaim($campaign, $client);
            if ($result and $num_claim < $campaign->gm_claim_now_btn_num_click) {

                $next($bot);
            } else {

                $this->notify($bot, $client);
            }
        }
    }

    protected function notify(Nutgram $bot, Client $client)
    {
        $text = "your are not legible to claim this campaign.sorry.";
        $bot->sendMessage(
            $text
        );
    }

    protected function clientCanClaim($campaign, $client)
    {
        $prime = true;
        $interestes = false;
        $geo = false;

        if (in_array($client->geo, $campaign->gm_geo)) {
            $geo = true;
        }
        foreach ($client->interestes as $int) {
            if (in_array($int, $campaign->gm_interest)) {
                $interestes = true;
                break;
            }
        }
        if (in_array("prime", $campaign->gm_interest)) {
            if (!$client->prime) {
                $prime = false;
            }
        }

        return $prime & $interestes & $geo;
    }
}

<?php

namespace App\Http\Bot\Middleware;

use SergiX44\Nutgram\Nutgram;
use App\Models\Client;
use App\Models\Campaign;
use Illuminate\Support\Arr;

class ClaimProduct
{
    public function __invoke(Nutgram $bot, $next)
    {
        $parameters = $this->getParameters($bot);
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $campaign = Campaign::firstWhere('id', $parameters['campaign_id']);
        $num_claim = $campaign->clients()->wherePivot('status', 1)->count();
        if (empty($campaign->gm_interest) and empty($campaign->gm_geo) and $num_claim < $campaign->gm_claim_now_btn_num_click) {
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

    protected function getParameters($bot)
    {

        $parameters = array();
        $text = $bot->message()->text;
        // start 1-1001618497163"
        $parameter = explode(" ", $text)[1];
        $campaign_id = explode("-", $parameter)[0];
        $target_chat_id = (int) "-" . explode("-", $parameter)[1];
        $parameters['campaign_id'] = $campaign_id;
        $parameters['target_chat_id'] = $target_chat_id;

        return $parameters;
    }

    protected function clientCanClaim($campaign, $client)
    {
        $prime = true;
        $interestes = false;
        $geo = true;

        if (!empty($campaign->gm_geo)) {

            if (!in_array($client->geo, $campaign->gm_geo)) {
                $geo = false;
            }
        }


        $gm_interestes = $campaign->gm_interest;
        if (($key = array_search('Prime', $gm_interestes)) !== false) {
            unset($gm_interestes[$key]);
        }
        if (!empty($gm_interestes)) {

            foreach ($client->interestes as $int) {
                if (in_array($int, $gm_interestes)) {
                    $interestes = true;
                    break;
                }
            }
        } else {
            $interestes = true;
        }

        if (in_array("Prime", $campaign->gm_interest) and !$client->prime) {
            $prime = false;
        }
        // dump($prime);
        // dump($interestes);
        // dump($geo);
        return $prime & $interestes & $geo;
    }
}

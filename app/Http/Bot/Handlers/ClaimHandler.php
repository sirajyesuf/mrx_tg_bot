<?php

namespace App\Http\Bot\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Handlers\Handler;
use App\Models\Campaign;
use App\Models\Client;
use Illuminate\Support\Str;
use App\services\CampaignService;
use Carbon\Carbon;

class ClaimHandler
{
    use Handler;

    public function __invoke(Nutgram $bot, $parameter)
    {
        $campaign_id = str::before($parameter, "-");
        $claim_target_chat_id = "-" . str::after($parameter, '-');
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $campaign = Campaign::firstWhere('id', $campaign_id);

        $message_id = CampaignService::send($bot, $client, $campaign);


        $client->campaigns()->attach($campaign_id, [
            'product_id' => $this->getProductId(),
            'tg_message_id' => $message_id,
            'claim_target_chat_id' => (int) $claim_target_chat_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }


    protected function getProductId()
    {
        $n = 7;
        $characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}

<?php

namespace App\Http\Bot\Handlers;

use App\Http\Bot\Handlers\Handler;
use SergiX44\Nutgram\Nutgram;
use App\Models\Client;
use App\Models\Campaign;
use App\services\CampaignService;
use Illuminate\Support\Carbon;
use App\Enums\ClaimStatus;

class DenyHandler
{
    use Handler;
    public  function __invoke(Nutgram $bot, $parameter)
    {
        $campaign  = Campaign::find($parameter);
        $client = Client::where('tg_user_id', $bot->chatId())->first();
        $claim = $client->campaigns()->wherePivot('campaign_id', $campaign->id)->first()->claim;
        $bottom_note = "#Denied at " . Carbon::now()->toDayDateTimeString();
        $client->campaigns()->updateExistingPivot($campaign->id, [
            'status' => ClaimStatus::Deny
        ]);
        CampaignService::editBotMessage($client, $campaign, $claim, $bottom_note);
    }
}

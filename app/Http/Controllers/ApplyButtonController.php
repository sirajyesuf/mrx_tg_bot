<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignClient;
use App\Models\Client;
use App\services\CampaignService;

class ApplyButtonController extends Controller
{

    public function apply(Client $client, Campaign $campaign)
    {

        $client->campaigns()->updateExistingPivot($campaign->id, [
            'status' => true,
        ]);

        $claim = CampaignClient::where('campaign_id', $campaign->id)
            ->where('client_id', $client->id)->first();
        CampaignService::editBotMessage($client, $campaign, $claim);
        return redirect($campaign->bm_apply_btn_url);
    }
}

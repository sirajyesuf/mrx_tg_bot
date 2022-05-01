<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignClient;
use App\Models\Client;
use App\services\CampaignService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Enums\ClaimStatus;

class ApplyButtonController extends Controller
{

    public function apply(Client $client, Campaign $campaign)
    {



        if ($campaign->num_applied_claim < $campaign->gm_claim_now_btn_num_click) {

            if ($client->campaigns()->wherePivot('campaign_id', $campaign->id)->wherePivot('status', ClaimStatus::Pending)->count() == 1) {

                $client->campaigns()->updateExistingPivot($campaign->id, [
                    'status' => ClaimStatus::Apply,
                ]);

                DB::table('campaigns')->increment('num_applied_claim');

                $claim = CampaignClient::where('campaign_id', $campaign->id)
                    ->where('client_id', $client->id)->first();

                $bottom_note = "#Applied at " . Carbon::now()->toDayDateTimeString();

                CampaignService::editBotMessage($client, $campaign, $claim, $bottom_note);

                return redirect($campaign->bm_apply_btn_url);
            } else {
                if ($client->campaigns()->wherePivot('campaign_id', $campaign->id)->wherePivot('status', ClaimStatus::Apply)->count() == 1) {
                    return "you already apply for the campaign.";
                } else {
                    return "please first claim the campaign from the group(s) or channels(s).";
                }
            }
        } else {

            return "too late to apply for this campaign " . $client->name . ".";
        }
    }
}

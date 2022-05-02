<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignClient;
use App\Models\Client;
use App\services\CampaignService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Enums\ClaimStatus;
use App\Message;

class ApplyButtonController extends Controller
{
    use Message;

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
                    return $this->apply_btn_ctr_already_applied;
                } else {
                    return $this->apply_btn_ctr_claim_first;
                }
            }
        } else {

            return $this->apply_btn_ctr_too_late_to_claim . $client->name . ".";
        }
    }
}

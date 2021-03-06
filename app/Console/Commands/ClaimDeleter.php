<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\Client;
use App\services\CampaignService;
use Illuminate\Support\Carbon;
use App\services\ClientService;
use Carbon\Carbon as CarbonCarbon;
use Filament\Forms\Components\Card;
use App\Models\CampaignClient;
use App\Enums\ClaimStatus;

class ClaimDeleter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claimdeleter:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete unapplyied claim and notify the client before the applying close.';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $num_mint_to_delete_pending_claim = (int) env('NUMBER_MINT_TO_DELETE_PENDING_CLAIM');
        // get all campaign where the num applyied claim lessthan the expected num of claim
        $campaigns = Campaign::whereColumn('num_applied_claim', '<', 'gm_claim_now_btn_num_click')->get();
        foreach ($campaigns as $campaign) {
            $now = Carbon::now();
            $dur = Carbon::parse($campaign->bm_apply_btn_active_duration)->diffAsCarbonInterval(Carbon::parse($campaign->updated_at));
            $notification_dur = Carbon::parse($campaign->bm_apply_btn_active_duration)->subMinutes($num_mint_to_delete_pending_claim)->diffAsCarbonInterval(Carbon::parse($campaign->updated_at));

            $clients = $campaign->clients()->wherePivot('status', ClaimStatus::Pending)->get();
            foreach ($clients as $client) {

                $past = Carbon::parse($client->claim->created_at);
                $res = $now->diffAsCarbonInterval($past);


                if ($res->greaterThanOrEqualTo($dur)) {
                    // delete the bot message from the client
                    CampaignService::deleteBotMessage($client);
                    CampaignClient::find($client->claim->id)->delete();
                }
                if ($res->greaterThanOrEqualTo($notification_dur) and $client->notification_status == 0) {
                    //send notification to client
                    $text = "hello $client->name please  apply the campaign with in the next min " . $num_mint_to_delete_pending_claim . " we are going to delete the campaign.";
                    ClientService::sendNotification($client, $text);
                }
            }
        }
    }
}

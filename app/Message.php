<?php

namespace App;

trait Message
{
    // my account
    public $my_account_introduction = "Alright,lets create account for you.";
    public $questions = [

        'geo' => "Please choose  your Geo name?",
        'prime' => "Do you have Prime account?",
        'interestes' => "Please select one or more of your interests?"

    ];




    //payment
    public $payout_already_requested = "you have already requested a payout. you will be notified when there was a problem or the payment was done.";


    //claim
    public $ucant_claim_dueto_interestes_or_geo_filtration = "your are not legible to claim this campaign.sorry.";
    public $ucant_claim_dueto_max_amount = "Sorry the product has already claimed the max. amount. There will be another product shortly available.";
    
}

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
    public $payment_start_message = 'Great. lets request payment for you.';
    public $payment_upload_proof = "please upload the proof. and if you need to give additional information please write it as caption.";
    public $payment_select_pay_mtd = "please select the payment method";
    public $payment_email_address = "Please Enter Your Email Address?";
    public $payment_confirmation = "please click the <b>Submit</b> button to send the payment request. <b>Cancel</b> button  to exit the process.";
    public $payment_select_campaign = "please select one of the following product id to request for payment.";
    public $payment_success_message = "the payment request send successfully. please await for approval.";
    //help menu
    public $help_text = "you need support please DM me\n\n@eerusuz3hf";
    //approved middleware
    public $approved_pending_acount = "your account is pending. please await for approval.";
    public $approved_denied_account = "your account is denied. please await for approval.";
    //authenticate middleware
    public $authenticate_text = "To claim the product you need to register one-time.use the \n<b>My Account</b> button from main menu.";
    //claimer middleare
    public $claimer_text = "📢 please first claim  and apply atleast one campaign.";
    //cliamonlyonce middleare
    public $claim_once_text = "your already claimed this campaign.";
    //claim
    public $ucant_claim_dueto_interestes_or_geo_filtration = "your are not legible to claim this campaign.sorry.";
    public $ucant_claim_dueto_max_amount = "Sorry the product has already claimed the max. amount. There will be another product shortly available.";
    //apply btn controller
    public $apply_btn_ctr_already_applied = "you already apply for the campaign.";
    public $apply_btn_ctr_claim_first = "please first claim the campaign from the group(s) or channels(s).";
    public $apply_btn_ctr_too_late_to_claim = "too late to apply for this campaign ";
}

<?php

namespace App\Http\Bot\Handlers;

use App\Http\Bot\Handlers\Handler;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use App\services\CampaignService;
use App\Models\Client;
use App\Http\Bot\Keyboard;
use App\Models\Campaign;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use SergiX44\Nutgram\Telegram\Types\Media\File;

class PaymentHandler extends Conversation
{
    use Handler;
    protected ?string $step = 'index';

    public function index(Nutgram $bot)
    {
        $order = [

            'campaign_id' => null,
            'proof' => null,
            'information' => null,
            'payment_method' => null


        ];
        $bot->setUserData('order', $order, $bot->chatId());
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $claims = $client->campaigns()->wherePivot('status', true)->get();
        $bot->sendMessage(
            'Alright lets request payment for you.',
            [
                'reply_markup' => Keyboard::cancelBtn()
            ]
        );
        $this->next('collectAnswer');
        $this->askCampaign($bot, $claims);
    }

    public function collectAnswer(Nutgram $bot)
    {
        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $claims = $client->campaigns()->wherePivot('status', true)->get();
        $order = $bot->getUserData('order');
        $callback_query = $bot->callbackQuery();
        $message = $bot->message();
        $que = $bot->getUserData('que');
        if ($bot->isCallbackQuery() and   in_array($callback_query->data, Campaign::all()->pluck('id')->toArray()) and $que = "askCampaign") {
            $campaign = Campaign::find($callback_query->data);
            $order['campaign_id'] = $campaign->id;
            $bot->setUserData('order', $order, $bot->chatId());
            CampaignService::requestPayment($bot, $campaign);
        }
        if ($bot->isCallbackQuery() and   str::of($callback_query->data)->contains('request_payment')) {
            $this->askToUploadProof($bot);
        }
        if ($bot->isCallbackQuery() and $callback_query->data == 'back_list_campaigns') {
            $this->askCampaign($bot, $claims);
        }
        if ($message and $que == 'asktouploadproof') {
            $photo = $message->photo[0];
            $photo_id = $photo->file_id;
            $order['proof'] = $photo_id;
            $order['information'] = $message->caption;
            // $file = $bot->getFile($photo_id);
            // $path = storage_path("orders/$photo_id");
            // $bot->downloadFile($file, $path);
            $bot->setUserData('order', $order, $bot->chatId());
            $this->askPaymentMethod($bot);
        }
        if ($bot->isCallbackQuery() and $que == "askpaymentmethod") {
            if ($callback_query->data == 'submit') {
                // create the order
                $client->orders()->create($order);
                // send sucess message
                $text = "the payment request send successfully. please await for approval.";
                $this->sendMessage($bot, $text, [
                    'reply_markup' => Keyboard::mainMenu()
                ]);
            }
            if (in_array($callback_query->data, Campaign::find($order['campaign_id'])->payment_methods)) {
                $order['payment_method'] = $callback_query->data;
                $bot->setUserData('order', $order, $bot->chatId());
                $this->askPaymentMethod($bot);
            }

            return;
        }
        if ($message) {
            if ($message->text == "❌Cancel") {


                $this->sendMessage($bot, 'cancelled.', [
                    'reply_markup' => Keyboard::mainMenu()
                ]);

                $bot->deleteUserData('order');
                $bot->deleteUserData('que');
            }
        }
    }

    protected function askCampaign($bot, $claims)
    {
        $campaigns = InlineKeyboardMarkup::make();
        $text = "please select one of the following product id to request for payment.";
        foreach ($claims as $claim) {
            $campaigns = $campaigns
                ->addRow(
                    InlineKeyboardButton::make(text: $claim->claim->product_id, callback_data: $claim->claim->campaign_id)
                );
        }
        $this->sendMessage(
            $bot,
            $text,
            [
                'reply_markup' => $campaigns
            ]
        );

        $bot->setUserData('que', 'askcampaign', $bot->chatId());
    }

    protected function askToUploadProof($bot)
    {
        $text = "please upload the proof. and if u need to give additional information pls write it as caption.";

        $this->sendMessage(
            $bot,
            $text,

        );

        $bot->setUserData('que', 'asktouploadproof', $bot->chatId());
    }

    protected function askPaymentMethod($bot)
    {
        $order = $bot->getUserData('order');
        $text = "please select payment method";
        $campaign = Campaign::find($order['campaign_id']);
        $pay_mtds = InlineKeyboardMarkup::make();
        foreach ($campaign->payment_methods as $pay_mtd) {

            if ($order['payment_method'] == $pay_mtd) {
                $pay_mtds = $pay_mtds
                    ->addRow(
                        InlineKeyboardButton::make(text: "✅$pay_mtd", callback_data: $pay_mtd)
                    );
            } else {
                $pay_mtds = $pay_mtds
                    ->addRow(
                        InlineKeyboardButton::make(text: $pay_mtd, callback_data: $pay_mtd)
                    );
            }
        }

        if ($order['payment_method']) {

            $pay_mtds = $pay_mtds
                ->addRow(
                    InlineKeyboardButton::make("✔️Submit", callback_data: "submit")
                );
        }

        $bot->setUserData('que', 'askpaymentmethod', $bot->chatId());



        $this->sendMessage(
            $bot,
            $text,
            [
                'reply_markup' => $pay_mtds
            ]
        );
    }
}

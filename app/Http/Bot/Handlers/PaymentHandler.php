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

class PaymentHandler extends Conversation
{
    use Handler;
    protected ?string $step = 'index';

    public function index(Nutgram $bot)
    {
        $order = [

            'campaign_id' => null,
            'proof' => null,
            'file_id' => null,
            'information' => null,
            'payment_method' => null,
            'payment_method_detail' => null,
            'email_address' => null


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

        dump($bot->message());

        $client = Client::firstWhere('tg_user_id', $bot->chatId());
        $claims = $client->campaigns()->wherePivot('status', true)->get();
        $order = $bot->getUserData('order');
        $callback_query = $bot->callbackQuery();
        $message = $bot->message();
        $que = $bot->getUserData('que');
        dump($que);

        if ($bot->isCallbackQuery() and   in_array((int)$callback_query->data, Campaign::all()->pluck('id')->toArray()) and $que == "askcampaign") {
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

            if ($message->text == "❌Cancel") {
                $this->cancelOrder($bot);
            } else {
                $photo = end($message->photo);
                $photo_id = $photo->file_id;
                $order['file_id'] = $photo_id;
                // name of the proof image
                $order['proof'] = $photo->file_unique_id . ".png";
                $order['information'] = $message->caption;
                $bot->setUserData('order', $order, $bot->chatId());
                $this->askPaymentMethod($bot);
            }
        }
        if ($message and $que == "askpaymentmethoddetail") {

            if ($message->text == "❌Cancel") {
                $this->cancelOrder($bot);
            } else {
                $order['payment_method_detail'] = $message->text;
                $bot->setUserData('order', $order, $bot->chatId());
                $this->askEmailAddress($bot);
            }
        }

        if ($message and $que == "askemailaddress") {



            if ($message->text == "❌Cancel") {
                $this->cancelOrder($bot);
            } else {
                $order['email_address'] = $message->text;
                $bot->setUserData('order', $order, $bot->chatId());
                $this->askConfirmation($bot);
            }
        }

        if ($message and $que == "askconfirmation") {

            $btn = $message->text;


            if ($btn == '✔️Submit') {
                // create the order
                $file = $bot->getFile($order['file_id']);
                $path = storage_path("app/public/" . $order['proof']);
                $res = $bot->downloadFile($file, $path);
                unset($order['file_id']);
                $order['proof'] = "storage/" . $order['proof'];
                $client->orders()->create($order);
                // send sucess message
                $text = "the payment request send successfully. please await for approval.";
                $this->sendMessage($bot, $text, [
                    'reply_markup' => Keyboard::mainMenu()
                ]);
            }

            if ($btn == '❌Cancel') {
                $this->cancelOrder($bot);
            }
        }


        if ($bot->isCallbackQuery() and $que == "askpaymentmethod") {

            if (in_array($callback_query->data, Campaign::find($order['campaign_id'])->payment_methods)) {
                $order['payment_method'] = $callback_query->data;
                $bot->setUserData('order', $order, $bot->chatId());
                $this->askPaymentMethodDetail($bot, strtolower($order['payment_method']));
            }

            return;
            // if ($callback_query->data == 'submit') {
            //     // create the order
            //     $file = $bot->getFile($order['file_id']);
            //     $path = storage_path("app/public/" . $order['proof']);
            //     $res = $bot->downloadFile($file, $path);
            //     unset($order['file_id']);
            //     $order['proof'] = "storage/" . $order['proof'];
            //     $client->orders()->create($order);
            //     // send sucess message
            //     $text = "the payment request send successfully. please await for approval.";
            //     $this->sendMessage($bot, $text, [
            //         'reply_markup' => Keyboard::mainMenu()
            //     ]);
            // }
        }
        if ($message) {
            if ($message->text == "❌Cancel") {
                $this->cancelOrder($bot);
            }
        }
    }

    protected function askCampaign($bot, $claims)
    {
        $campaigns = InlineKeyboardMarkup::make();
        $text = "please select one of the following product id to request for payment.";
        foreach ($claims as $claim) {
            $btn_text =  $claim->title . "( ID: " . $claim->claim->product_id . ")";
            $campaigns = $campaigns
                ->addRow(
                    InlineKeyboardButton::make(text: $btn_text, callback_data: $claim->claim->campaign_id)
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

            // if ($order['payment_method'] == $pay_mtd) {
            //     $pay_mtds = $pay_mtds
            //         ->addRow(
            //             InlineKeyboardButton::make(text: "✅$pay_mtd", callback_data: $pay_mtd)
            //         );
            // } 
            // else {
            $pay_mtds = $pay_mtds
                ->addRow(
                    InlineKeyboardButton::make(text: $pay_mtd, callback_data: $pay_mtd)
                );
            // }
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
    protected function askPaymentMethodDetail($bot, $pay_mtd)
    {
        $text = null;
        if ($pay_mtd == "crypto") {
            $text = "please enter your crypto wallet address?";
        }
        if ($pay_mtd == "amazone") {
            $text = "please enter your amazone account?";
        }
        if ($pay_mtd == "paypal") {
            $text = "please enter your paypal account?";
        }

        $this->sendMessage(
            $bot,
            $text
        );



        $bot->setUserData('que', 'askpaymentmethoddetail', $bot->chatId());
    }

    protected function askEmailAddress($bot)
    {
        $text = "please enter your Email Address?";
        $this->sendMessage(
            $bot,
            $text
        );
        $bot->setUserData('que', 'askemailaddress', $bot->chatId());
    }

    protected function askConfirmation($bot)
    {
        $text = "please click the <b>Submit</b> button to send the payment request. <b>Cancel</b> button  to exit the process.";
        $this->sendMessage(
            $bot,
            $text,
            [
                'parse_mode' => 'html',
                'reply_markup' => Keyboard::requestOrderConfirmation()
            ]
        );
        $bot->setUserData('que', 'askconfirmation', $bot->chatId());
    }

    protected function cancelOrder($bot)
    {

        $this->sendMessage($bot, 'cancelled.', [
            'reply_markup' => Keyboard::mainMenu()
        ]);

        $bot->deleteUserData('order');
        $bot->deleteUserData('que');
    }
}

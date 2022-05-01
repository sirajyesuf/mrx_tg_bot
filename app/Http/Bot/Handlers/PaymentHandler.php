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
use App\Enums\Stats;
use App\Models\Order;
use App\Message;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PaymentHandler extends Conversation
{
    use Handler;
    use Message;
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
        $claims = $client->campaigns()->wherePivot('status', ClaimStatus::Apply)->get();
        $text = $this->payment_start_message;
        $bot->sendMessage(
            $text,
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
        $claims = $client->campaigns()->wherePivot('status', ClaimStatus::Apply)->get();
        $order = $bot->getUserData('order');
        $callback_query = $bot->callbackQuery();
        $message = $bot->message();
        $que = $bot->getUserData('que');

        if ($bot->isCallbackQuery() and   in_array((int)$callback_query->data, Campaign::all()->pluck('id')->toArray()) and $que == "askcampaign") {
            $campaign = Campaign::find($callback_query->data);
            $approved_n_pending_order  = DB::table('orders')
                ->where([
                    ['campaign_id', '=', $campaign->id],
                    ['client_id', '=', $client->id],
                    ['status', '=', OrderStatus::Approve]

                ])
                ->orWhere([
                    ['campaign_id', '=', $campaign->id],
                    ['client_id', '=', $client->id],
                    ['status', '=', OrderStatus::Pending]
                ])
                ->get();

            if ($approved_n_pending_order->count()) {
                $bot->sendMessage(
                    $this->payout_already_requested
                );
                $this->askCampaign($bot, $claims);
            } else {
                $order['campaign_id'] = $campaign->id;
                $bot->setUserData('order', $order, $bot->chatId());
                CampaignService::requestPayment($bot, $campaign);
            }
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
                $text = $this->payment_success_message;
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
                $this->askPaymentMethodDetail($bot, $order['payment_method']);
            }

            return;
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
        $text = $this->payment_select_campaign;
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
        $text = $this->payment_upload_proof;

        $this->sendMessage(
            $bot,
            $text,
        );

        $bot->setUserData('que', 'asktouploadproof', $bot->chatId());
    }

    protected function askPaymentMethod($bot)
    {
        $order = $bot->getUserData('order');
        $text = $this->payment_select_pay_mtd;
        $campaign = Campaign::find($order['campaign_id']);
        $pay_mtds = InlineKeyboardMarkup::make();
        foreach ($campaign->payment_methods as $pay_mtd) {

            $pay_mtds = $pay_mtds
                ->addRow(
                    InlineKeyboardButton::make(text: $pay_mtd, callback_data: $pay_mtd)
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
    protected function askPaymentMethodDetail($bot, $pay_mtd)
    {
        $pay_mtd = Payment::where('name', $pay_mtd)->first();

        $this->sendMessage(
            $bot,
            $pay_mtd->message
        );



        $bot->setUserData('que', 'askpaymentmethoddetail', $bot->chatId());
    }

    protected function askEmailAddress($bot)
    {
        $text = $this->payment_email_address;
        $this->sendMessage(
            $bot,
            $text
        );
        $bot->setUserData('que', 'askemailaddress', $bot->chatId());
    }

    protected function askConfirmation($bot)
    {
        $text = $this->payment_confirmation;
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

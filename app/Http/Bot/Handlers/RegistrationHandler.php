<?php

namespace App\Http\Bot\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use App\Http\Bot\Handlers\Handler;
use App\Models\Interest;
use App\Models\Country;
use App\Models\Client;
use App\Http\Bot\Keyboard;

class RegistrationHandler extends Conversation
{
    use Handler;
    protected ?string $step = 'show';

    protected $questions = [

        'geo' => "Please choose  your Geo name?",
        'prime' => "Do you have Prime account?",
        'interestes' => "Please select one or more of your interests?"

    ];
    public function show(Nutgram $bot)
    {

        $user = Client::firstWhere('tg_user_id', $bot->chatId());
        if ($user) {
            $this->profile($bot, $user);
            $this->end();
        } else {
            $this->create($bot);
        }
    }

    public function create(Nutgram $bot)
    {
        $text = "Alright,lets create account for you.";
        $btn = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('❌Cancel')
            );

        $fields = [
            'first_name' => $bot->user()->first_name,
            'last_name' => $bot->user()->last_name,
            'tg_user_id' => $bot->chatId(),
            'tg_username' => $bot->user()->username,
            'geo' => null,
            'interestes' => [],
            'prime' => null,
        ];
        $bot->setUserData('account', $fields, $bot->chatId());
        $bot->sendMessage($text, ['reply_markup' => $btn]);
        $this->askCountry($bot);
        $this->next('collectAnswer');
    }



    public function collectAnswer(Nutgram $bot)
    {
        $callback_query = $bot->callbackQuery();
        $message = $bot->message();
        if ($bot->isCallbackQuery() & $bot->getUserData('que') === 'geo') {
            $data = $callback_query->data;
            $fields = $bot->getUserData('account', $bot->chatId());
            if ($data == $fields['geo']) {
                $fields['geo'] = null;
            } else {
                $fields['geo'] = $data;
            }
            $bot->setUserData('account', $fields, $bot->chatId());
            $bot->sendMessage("your Geo:<b>$data</b>", [
                'parse_mode' => 'html'
            ]);
            $this->askPrime($bot);
            return;
        }

        if ($bot->isCallbackQuery() & $bot->getUserData('que') === 'prime') {
            $data = $callback_query->data;
            $fields = $bot->getUserData('account', $bot->chatId());
            $fields['prime'] = $data == 'yes' ? true : false;
            $bot->setUserData('account', $fields, $bot->chatId());
            $bot->sendMessage(
                "do you have prime account: <b>$data</b>",
                [
                    'parse_mode' => 'html'
                ]
            );
            $this->askInterests($bot);
            return;
        }
        if ($bot->isCallbackQuery() & $bot->getUserData('que') === 'interestes') {
            if ($callback_query->data == "submit") {
                $fields = $bot->getUserData('account', $bot->chatId());
                $client = Client::create($fields);
                $this->profile($bot, $client);
            } else {
                $data = $callback_query->data;
                $fields = $bot->getUserData('account', $bot->chatId());
                if (in_array($data, $fields['interestes'])) {
                    $fields['interestes'] = array_diff($fields['interestes'], array($data));
                } else {
                    $fields['interestes'][] = $data;
                }
                $bot->setUserData('account', $fields, $bot->chatId());
                $this->askInterests($bot);
            }
            return;
        }

        if ($message) {
            if ($message->text == "❌Cancel") {

                $this->sendMessage($bot, 'cancelled.', [
                    'reply_markup' => Keyboard::mainMenu()
                ]);
            }
        }
    }

    protected function askCountry(Nutgram $bot)
    {
        $text = $this->questions['geo'];
        $countries = Country::all();
        $fields = $bot->getUserData('account', $bot->chatId());
        $btn = InlineKeyboardMarkup::make();

        foreach ($countries as $ctry) {

            if ($ctry->id == $fields['geo']) {
                $btn = $btn->addRow(
                    InlineKeyboardButton::make("✅$ctry->name", callback_data: "$ctry->name")
                );
            } else {
                $btn = $btn->addRow(
                    InlineKeyboardButton::make("$ctry->name", callback_data: "$ctry->name")
                );
            }
        }
        $this->sendMessage(
            $bot,
            $text,
            [
                'reply_markup' => $btn
            ]
        );
        $bot->setUserData('que', 'geo', $bot->chatId());
    }

    protected function askPrime(Nutgram $bot)
    {
        $text = $this->questions['prime'];
        $btn = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make("Yes", callback_data: "yes"))
            ->addRow(InlineKeyboardButton::make("No", callback_data: "no"));
        $this->sendMessage(
            $bot,
            $text,
            [
                'reply_markup' => $btn
            ]
        );
        $bot->setUserData('que', 'prime', $bot->chatId());
    }

    protected function askInterests(Nutgram $bot)
    {
        $text = $this->questions['interestes'];
        $interestes = Interest::where('name', '!=', 'prime')->get();
        $fields = $bot->getUserData('account', $bot->chatId());
        $btn = InlineKeyboardMarkup::make();

        foreach ($interestes as $fav) {

            if (in_array($fav->name, $fields['interestes'])) {
                $btn = $btn->addRow(
                    InlineKeyboardButton::make("✅$fav->name", callback_data: "$fav->name"),

                );
            } else {
                $btn = $btn->addRow(
                    InlineKeyboardButton::make("$fav->name", callback_data: "$fav->name")
                );
            }
        }
        if (count($fields['interestes'])) {
            $btn = $btn->addRow(
                InlineKeyboardButton::make("✔️Submit", callback_data: "submit")
            );
        }
        $this->sendMessage(
            $bot,
            $text,
            [
                'reply_markup' => $btn
            ]
        );
        $bot->setUserData('que', 'interestes', $bot->chatId());
    }

    protected function profile(Nutgram $bot, Client $user)
    {
        $int = "";
        foreach ($user->interestes as $fav) {
            $int = $int . "\t\t\t\t☑️" . $fav . "\n";
        }
        $_prime = $user->prime ? 'Yes' : 'No';
        $text = "👤 My Account\n\n<b>🌍Geo:</b> $user->geo\n\n<b>⭐Interestes:</b> \n$int\n<b>🎥Prime:</b>$_prime";
        $this->sendMessage($bot, $text, [
            'reply_markup' => Keyboard::mainMenu(),
            'parse_mode' => 'html'
        ]);
    }
}

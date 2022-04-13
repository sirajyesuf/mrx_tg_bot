<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Middleware\SetGlobalData;
use App\Http\Bot\Middleware\VerifyMember;
use App\Http\Bot\Middleware\Authenticate;
use App\Http\Bot\Middleware\Approved;
use App\Http\Bot\Middleware\OnlyClaimOnce;
use App\Http\Bot\Middleware\ClaimProduct;
use App\Http\Bot\Middleware\Claimer;
use App\Http\Bot\Handlers\StartHandler;
use App\Http\Bot\Handlers\RegistrationHandler;
use App\Http\Bot\Handlers\ClaimHandler;
use App\Http\Bot\Handlers\HelpHandler;
use App\Http\Bot\Handlers\PaymentHandler;
use App\Http\Bot\Handlers\DenyHandler;

$bot->onCommand('start', StartHandler::class)->middleware(SetGlobalData::class);
$bot->onCommand('start {parameter}', ClaimHandler::class)
    ->middleware(OnlyClaimOnce::class)
    ->middleware(Approved::class)
    ->middleware(Authenticate::class);
$bot->onText('Account', RegistrationHandler::class)
    ->middleware(VerifyMember::class);
$bot->onText('Payment', PaymentHandler::class)
    ->middleware(Claimer::class)
    ->middleware(Approved::class)
    ->middleware(Authenticate::class);

$bot->onText('Help', HelpHandler::class);
$bot->onCallbackQueryData('deny {parameter}', DenyHandler::class);
$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Sorry, I don\'t understand.');
});

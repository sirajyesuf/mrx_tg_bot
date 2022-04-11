<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Middleware\SetGlobalData;
use App\Http\Bot\Middleware\VerifyMember;
use App\Http\Bot\Middleware\Authenticate;
use App\Http\Bot\Middleware\Approved;
use App\Http\Bot\Middleware\ClaimProduct;
use App\Http\Bot\Middleware\Claimer;
use App\Http\Bot\Handlers\StartHandler;
use App\Http\Bot\Handlers\RegistrationHandler;
use App\Http\Bot\Handlers\ClaimHandler;
use App\Http\Bot\Handlers\HelpHandler;
use App\Http\Bot\Handlers\PaymentHandler;

$bot->onCommand('start', StartHandler::class)->middleware(SetGlobalData::class);
$bot->onCommand('start {parameter}', ClaimHandler::class)
    ->middleware(Authenticate::class)
    ->middleware(Approved::class)
    ->middleware(ClaimProduct::class);
$bot->onText('Account', RegistrationHandler::class)->middleware(SetGlobalData::class)->middleware(VerifyMember::class);
$bot->onText('Help', HelpHandler::class);
$bot->onText('Payment', PaymentHandler::class)
    ->middleware(Authenticate::class)
    ->middleware(Approved::class)
    ->middleware(Claimer::class);

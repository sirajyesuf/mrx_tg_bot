<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Http\Bot\Middleware\SetGlobalData;
use App\Http\Bot\Middleware\VerifyMember;
use App\Http\Bot\Handlers\StartHandler;
use App\Http\Bot\Handlers\RegistrationHandler;

$bot->onCommand('start', StartHandler::class)->middleware(SetGlobalData::class);
$bot->onText('Account', RegistrationHandler::class)->middleware(SetGlobalData::class)->middleware(VerifyMember::class);

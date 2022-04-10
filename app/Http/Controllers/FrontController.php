<?php

namespace App\Http\Controllers;

use SergiX44\Nutgram\Nutgram;
use App\Http\Controllers\Controller;

class FrontController extends Controller
{
    public function __invoke(Nutgram $bot)
    {

        $bot->run();
    }
}

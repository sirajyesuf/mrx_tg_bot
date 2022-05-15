<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplyButtonController;
use App\Http\Controllers\BinancePay;


Route::get('/', function () {

    return redirect(route('filament.auth.login'));
});

Route::get('/apply_btn/{client}/{campaign}', [ApplyButtonController::class, 'apply'])
    ->name('apply_btn');
Route::post('/binance_webhook',[BinancePay::class,'webhook']);

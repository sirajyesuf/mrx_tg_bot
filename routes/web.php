<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplyButtonController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/apply_btn/{client}/{campaign}', [ApplyButtonController::class, 'apply'])
    ->name('apply_btn');

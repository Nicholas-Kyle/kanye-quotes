<?php

use App\Http\Controllers\KanyeQuoteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'store']);

Route::middleware(['auth.api'])->group(function () {
    Route::get('/kanye-quotes', [KanyeQuoteController::class, 'index']);
    Route::post('/kanye-quotes/refresh', [KanyeQuoteController::class, 'refresh']);
});

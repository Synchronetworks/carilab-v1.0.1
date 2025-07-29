<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\API\WalletsController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('wallet-top-up', [ WalletsController::class, 'walletTopup' ] );
    Route::get('wallet-history', [ WalletsController::class, 'getHistory' ] );
    Route::post('withdraw-money', [WalletsController::class, 'withdrawMoney']);
});

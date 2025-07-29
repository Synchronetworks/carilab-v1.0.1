<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Models\Role;
use Modules\Payout\Http\Controllers\API\PayoutsController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('collector-payout-list', [ PayoutsController::class, 'collectorPayoutList' ] );
});


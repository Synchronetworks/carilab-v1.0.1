<?php

use Modules\Coupon\Http\Controllers\Backend\CouponsController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('coupon-list',[CouponsController::class, 'index_list' ]);
   
});

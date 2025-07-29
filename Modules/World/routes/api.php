<?php

use Illuminate\Support\Facades\Route;
use Modules\World\Http\Controllers\API\WorldsController;


Route::get('country-list',[ WorldsController::class, 'getCountryList' ]);
Route::get('state-list',[ WorldsController::class, 'getStateList' ]);
Route::get('city-list',[ WorldsController::class, 'getCityList' ]);
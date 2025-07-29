<?php

use Illuminate\Support\Facades\Route;
use Modules\CatlogManagement\Http\Controllers\API\CatlogManagementsController;


Route::get('testcase-list',[ CatlogManagementsController::class, 'catlogList' ]);
Route::get('testcase-detail',[ CatlogManagementsController::class, 'getCatlogDetail' ]);
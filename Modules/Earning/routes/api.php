<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Models\Role;
use Modules\Earning\Http\Controllers\Backend\EarningsController;

Route::group(['middleware' => ['auth:sanctum']], function () {
Route::get('collector-earning-list',[EarningsController::class, 'index_data' ]);
});
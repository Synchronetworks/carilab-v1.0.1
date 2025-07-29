<?php

use Modules\Bank\Http\Controllers\Backend\BanksController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum']], function () {
Route::post('add-bank', [BanksController::class, 'store']);
Route::post('update-bank/{id}', [BanksController::class, 'update']);
Route::post('delete-bank/{id}', [BanksController::class, 'destroy']);
Route::get('bank-list', [Modules\Bank\Http\Controllers\API\BanksController::class, 'bankList']);
});


?>  
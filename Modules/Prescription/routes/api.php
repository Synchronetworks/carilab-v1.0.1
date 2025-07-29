<?php

use Illuminate\Support\Facades\Route;
use Modules\Prescription\Http\Controllers\Backend\PrescriptionsController;



Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('save-prescription', [PrescriptionsController::class, 'store']);
    Route::get('prescription-list', [Modules\Prescription\Http\Controllers\API\PrescriptionsController::class, 'prescriptionList']);
    Route::get('prescription-detail', [Modules\Prescription\Http\Controllers\API\PrescriptionsController::class, 'prescriptionDetail']);

});
?>
<?php

use Illuminate\Support\Facades\Route;
use Modules\Lab\Http\Controllers\API\LabsApiController;
use Modules\Lab\Http\Controllers\Backend\LabSessionController;



Route::get('lab-list', [LabsApiController::class, 'lablist']);
Route::get('lab-detail', [LabsApiController::class, 'labDetail']);
Route::get('get-time-slots', [LabSessionController::class, 'availableSlot']);
?>

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\API;

Route::group(['middleware' => ['auth:sanctum']], function () {
Route::get('helpdesk-list', [ \Modules\Helpdesk\Http\Controllers\API\HelpdesksController::class, 'getHelpDeskList' ] );
Route::Post('helpdesk-save', [ \Modules\Helpdesk\Http\Controllers\Backend\HelpdesksController::class, 'store' ] );
Route::Post('helpdesk-closed/{id}', [ \Modules\Helpdesk\Http\Controllers\Backend\HelpdesksController::class, 'closed' ] );
Route::get('helpdesk-detail', [ \Modules\Helpdesk\Http\Controllers\API\HelpdesksController::class, 'getHelpDeskDetail' ] );
Route::post('helpdesk-activity-save/{id}', [\Modules\Helpdesk\Http\Controllers\Backend\HelpdesksController::class, 'activity']);
});
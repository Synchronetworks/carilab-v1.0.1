<?php

use Illuminate\Support\Facades\Route;
use Modules\Collector\Http\Controllers\Backend\CollectorsController;
use Modules\Collector\Http\Controllers\Backend\CollectorDocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_collector']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Collectors Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'collectors', 'as' => 'collectors.'],function () {
      Route::get("list/{approval_status?}", [CollectorsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [CollectorsController::class, 'index_data'])->name("index_data");
      Route::get('export', [CollectorsController::class, 'export'])->name('export');
      Route::get('collectors/{' . 'collectors' . '}/edit', [CollectorsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [CollectorsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [CollectorsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [CollectorsController::class, 'forceDelete'])->name('force_delete');
      Route::get('details/{id}', [CollectorsController::class, 'details'])->name('details');
      Route::get('changepassword/{id}', [CollectorsController::class, 'changepassword'])->name('changepassword');
      Route::post('update-password/{id}', [CollectorsController::class, 'updatePassword'])->name('update_password');
    
    });
 
    Route::resource("collectors", CollectorsController::class);

  

    // Backend Collector Documents Routes
    Route::group(['prefix' => 'collectordocument','as' => 'collectordocument.'], function () {
     
        Route::get('index-data', [CollectorDocumentController::class, 'index_data'])->name('index_data');
        Route::post('bulk-action', [CollectorDocumentController::class, 'bulk_action'])->name('bulk_action');
        Route::get('export', [CollectorDocumentController::class, 'export'])->name('export');
        Route::post('action', [CollectorDocumentController::class, 'action'])->name('action');
        Route::post('{id}', [CollectorDocumentController::class, 'destroy'])->name('destroy');
        Route::post('update-required/{id}', [CollectorDocumentController::class, 'update_required'])->name('update_required');
        Route::post('restore/{id}', [CollectorDocumentController::class, 'restore'])->name('restore');
        Route::delete('force-delete/{id}', [CollectorDocumentController::class, 'forceDelete'])->name('force_delete');
        Route::post('update-status/{id}', [CollectorDocumentController::class, 'update_status'])->name('update_status');

    });
    Route::get('documents/check-required', [CollectorDocumentController::class, 'checkRequired'])
    ->name('documents.check-required');

    Route::resource('collectordocument', CollectorDocumentController::class);
});




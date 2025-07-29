<?php

use Illuminate\Support\Facades\Route;
use Modules\Commision\Http\Controllers\Backend\CommisionsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Commisions Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'commisions', 'as' => 'commisions.'],function () {
      Route::get("index_list", [CommisionsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [CommisionsController::class, 'index_data'])->name("index_data");
      Route::get('export/{user_type}', [CommisionsController::class, 'export'])->name('export');
      Route::get('commisions/{' . 'commisions' . '}/edit', [CommisionsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [CommisionsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [CommisionsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [CommisionsController::class, 'forceDelete'])->name('force_delete');
      Route::post('update-status/{id}', [CommisionsController::class, 'update_status'])->name('update_status');

    });
    Route::resource("commisions", CommisionsController::class);
    Route::group(['middleware' => ['permission:view_collector_commisions']], function () {
    Route::get('collector-commisions', [CommisionsController::class, 'index'])->name('collector_commisions.index')->defaults('user_type', 'collector');
    Route::get('collector-commisions/create', [CommisionsController::class, 'create'])->name('collector_commisions.create')->defaults('user_type', 'collector');
    });

    // Vendor Bank Routes
    Route::group(['middleware' => ['permission:view_vendor_commisions']], function () {
    Route::get('vendor-commisions', [CommisionsController::class, 'index'])->name('vendor_commisions.index')->defaults('user_type', 'vendor');
    Route::get('vendor-commisions/create', [CommisionsController::class, 'create'])->name('vendor_commisions.create')->defaults('user_type', 'vendor');
    });
  });





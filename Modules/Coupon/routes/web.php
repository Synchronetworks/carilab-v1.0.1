<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\Backend\CouponsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_coupons']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Coupons Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'coupons', 'as' => 'coupons.'],function () {
      Route::get("index_list", [CouponsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [CouponsController::class, 'index_data'])->name("index_data");
      Route::get('export', [CouponsController::class, 'export'])->name('export');
      Route::get('coupons/{' . 'coupons' . '}/edit', [CouponsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [CouponsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [CouponsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [CouponsController::class, 'forceDelete'])->name('force_delete');
      Route::post('update-status/{id}', [CouponsController::class, 'update_status'])->name('update_status');
      Route::get('/get-tests-packages/{vendor_id}', [CouponsController::class, 'getTestsAndPackages'])
      ->name('tests-packages');

    });
    Route::resource("coupons", CouponsController::class);
});




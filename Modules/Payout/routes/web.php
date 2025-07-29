<?php

use Illuminate\Support\Facades\Route;
use Modules\Payout\Http\Controllers\API\PayoutsController as APIPayoutsController;
use Modules\Payout\Http\Controllers\Backend\PayoutsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_vendor_payouts|view_collector_payouts']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Payouts Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'payouts', 'as' => 'payouts.'],function () {
      Route::get("index_list", [PayoutsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [PayoutsController::class, 'index_data'])->name("index_data");
      Route::get('export/{user_type?}', [PayoutsController::class, 'export'])->name('export');
      Route::get('payouts/{' . 'payouts' . '}/edit', [PayoutsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [PayoutsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [PayoutsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [PayoutsController::class, 'forceDelete'])->name('force_delete');
      Route::post('vendor-store', [PayoutsController::class, 'vendor_store'])->name('vendor_store');
      Route::get('vendor-create', [PayoutsController::class, 'vendor_create'])->name('vendor_create');
      Route::get('vendor-index', [PayoutsController::class, 'vendor_index'])->name('vendor_index');
      Route::get('vendor-index-data', [PayoutsController::class, 'vendor_index_data'])->name('vendor_index_data');
      Route::post('initialize-payment', [PayoutsController::class, 'processPaymentGateway'])
      ->name('initialize-payment');
      Route::get('/payment/success', [PayoutsController::class, 'handlePaymentSuccess'])
    ->name('payment.success');
    });
    Route::resource("payouts", PayoutsController::class);
});




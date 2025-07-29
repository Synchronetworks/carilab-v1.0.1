<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\Backend\WalletsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_wallet']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Wallets Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'wallets', 'as' => 'wallets.'],function () {
      Route::get("index_list", [WalletsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [WalletsController::class, 'index_data'])->name("index_data");
      Route::get('export', [WalletsController::class, 'export'])->name('export');
      Route::get('wallets/{' . 'wallets' . '}/edit', [WalletsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [WalletsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [WalletsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [WalletsController::class, 'forceDelete'])->name('force_delete');
    });
    Route::resource("wallets", WalletsController::class);
});




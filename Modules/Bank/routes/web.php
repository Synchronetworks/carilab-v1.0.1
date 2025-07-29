<?php

use Illuminate\Support\Facades\Route;
use Modules\Bank\Http\Controllers\Backend\BanksController;



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
     *  Backend Banks Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'banks', 'as' => 'banks.'],function () {
      Route::get("index_list", [BanksController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [BanksController::class, 'index_data'])->name("index_data");
      Route::get('export/{user_type}', [BanksController::class, 'export'])->name('export');
      Route::get('banks/{' . 'banks' . '}/edit', [BanksController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [BanksController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [BanksController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [BanksController::class, 'forceDelete'])->name('force_delete');
    });
    Route::resource("banks", BanksController::class);
    Route::group(['middleware' => ['permission:view_collector_bank']], function () {
    // Collector Bank Routes
    Route::get('collector-bank', [BanksController::class, 'index'])->name('collector_bank.index')->defaults('user_type', 'collector');
    Route::get('collector-bank/create', [BanksController::class, 'create'])->name('collector_bank.create')->defaults('user_type', 'collector');
    });
    Route::group(['middleware' => ['permission:view_vendor_bank']], function () {
    // Vendor Bank Routes
    Route::get('vendor-bank', [BanksController::class, 'index'])->name('vendor_bank.index')->defaults('user_type', 'vendor');
    Route::get('vendor-bank/create', [BanksController::class, 'create'])->name('vendor_bank.create')->defaults('user_type', 'vendor');
    });
});




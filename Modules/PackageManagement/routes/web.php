<?php

use Illuminate\Support\Facades\Route;
use Modules\PackageManagement\Http\Controllers\Backend\PackageManagementsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_packages']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend PackageManagements Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'packagemanagements', 'as' => 'packagemanagements.'],function () {
      Route::get("index_list", [PackageManagementsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [PackageManagementsController::class, 'index_data'])->name("index_data");
      Route::get('export', [PackageManagementsController::class, 'export'])->name('export');
      Route::get('packagemanagements/{' . 'packagemanagements' . '}/edit', [PackageManagementsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [PackageManagementsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [PackageManagementsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [PackageManagementsController::class, 'forceDelete'])->name('force_delete');
    });
    Route::resource("packagemanagements", PackageManagementsController::class);
});






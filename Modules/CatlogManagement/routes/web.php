<?php

use Illuminate\Support\Facades\Route;
use Modules\CatlogManagement\Http\Controllers\Backend\CatlogManagementsController;
use Modules\Constant\Http\Controllers\Backend\ConstantsController;


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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth', 'permission:view_catelog']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend CatlogManagements Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'catlogmanagements', 'as' => 'catlogmanagements.'],function () {
      Route::get("index_list", [CatlogManagementsController::class, 'index_list'])->name("index_list");
      Route::get("test_list", [CatlogManagementsController::class, 'test_list'])->name("test_list");
      Route::get("index_data", [CatlogManagementsController::class, 'index_data'])->name("index_data");
      Route::get('export', [CatlogManagementsController::class, 'export'])->name('export');
      Route::get('catlogmanagements/{' . 'catlogmanagements' . '}/edit', [CatlogManagementsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [CatlogManagementsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [CatlogManagementsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [CatlogManagementsController::class, 'forceDelete'])->name('force_delete');
    });
    Route::resource("catlogmanagements", CatlogManagementsController::class);
    Route::post('constants/store', [ConstantsController::class, 'store'])->name('store');
});




<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Backend\CategoriesController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth', 'permission:view_category']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Categories Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'categories', 'as' => 'categories.'],function () {
      Route::get("index_list", [CategoriesController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [CategoriesController::class, 'index_data'])->name("index_data");
      Route::get('export', [CategoriesController::class, 'export'])->name('export');
      Route::get('categories/{' . 'categories' . '}/edit', [CategoriesController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [CategoriesController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [CategoriesController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [CategoriesController::class, 'forceDelete'])->name('force_delete');
      Route::post('categories/update_status/{id}', [CategoriesController::class, 'update_status'])->name('update_status');
    });
    Route::resource("categories", CategoriesController::class);
});




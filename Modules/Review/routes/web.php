<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\Http\Controllers\Backend\ReviewsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_reviews']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Reviews Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'reviews', 'as' => 'reviews.'],function () {
      Route::get("index_list", [ReviewsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [ReviewsController::class, 'index_data'])->name("index_data");
      Route::get('export', [ReviewsController::class, 'export'])->name('export');
      Route::get('reviews/{' . 'reviews' . '}/edit', [ReviewsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [ReviewsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [ReviewsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [ReviewsController::class, 'forceDelete'])->name('force_delete');
    });
    Route::resource("reviews", ReviewsController::class);
});




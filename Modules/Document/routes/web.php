<?php

use Illuminate\Support\Facades\Route;
use Modules\Document\Http\Controllers\Backend\DocumentsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth', 'permission:view_documents']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Documents Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'documents', 'as' => 'documents.'],function () {
      Route::get("index_list", [DocumentsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [DocumentsController::class, 'index_data'])->name("index_data");
      Route::get('export', [DocumentsController::class, 'export'])->name('export');
      Route::get('documents/{' . 'documents' . '}/edit', [DocumentsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [DocumentsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [DocumentsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [DocumentsController::class, 'forceDelete'])->name('force_delete');
      Route::post('update-status/{id}', [DocumentsController::class, 'update_status'])->name('update_status');
      Route::post('update_required/{id}', [DocumentsController::class, 'update_required'])->name('update_required');
    });
    Route::resource("documents", DocumentsController::class);
});




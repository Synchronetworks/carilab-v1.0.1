<?php

use Illuminate\Support\Facades\Route;
use Modules\Prescription\Http\Controllers\Backend\PrescriptionsController;
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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_prescription']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Prescriptions Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'prescriptions', 'as' => 'prescriptions.'],function () {
      Route::get("list/{approval_status?}", [PrescriptionsController::class, 'pending'])->name("pending");
      Route::get("index_list", [PrescriptionsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [PrescriptionsController::class, 'index_data'])->name("index_data");
      Route::get('export/{prescriptions_status?}', [PrescriptionsController::class, 'export'])->name('export');
      Route::get('prescriptions/{' . 'prescriptions' . '}/edit', [PrescriptionsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [PrescriptionsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [PrescriptionsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [PrescriptionsController::class, 'forceDelete'])->name('force_delete');
      Route::get('view-document/{id}', [PrescriptionsController::class, 'viewDocument'])->name('view_document');
      Route::get('download-document/{id}', [PrescriptionsController::class, 'downloadDocument'])->name('download_document');
      Route::post('add-selection/{id}', [PrescriptionsController::class, 'addSelection'])->name('add_selection');
      Route::post('remove-test/{id}', [PrescriptionsController::class, 'removeTest'])->name('remove_test');
      Route::post('send-suggestion/{id}', [PrescriptionsController::class, 'sendSuggestion'])->name('send_suggestion');
    });
    Route::resource("prescriptions", PrescriptionsController::class);
});







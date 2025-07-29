<?php

use Illuminate\Support\Facades\Route;
use Modules\Lab\Http\Controllers\Backend\LabsController;
use Modules\Lab\Http\Controllers\Backend\LabSessionController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_lab']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *  
     *  Backend Labs Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'labs', 'as' => 'labs.'],function () {
      Route::get("index_list", [LabsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [LabsController::class, 'index_data'])->name("index_data");
      Route::get('export', [LabsController::class, 'export'])->name('export');
      Route::get('labs/{' . 'labs' . '}/edit', [LabsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [LabsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [LabsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [LabsController::class, 'forceDelete'])->name('force_delete');
      Route::get('labDetails/{id}', [LabsController::class, 'details'])->name('details');
      Route::get('import',[LabsController::class,'import'])->name('import');
      Route::post('importLab',[LabsController::class,'importLab'])->name('importLab');
      // Route::get('labDetails/{id}/{section}', [LabsController::class, 'loadSection'])->name('details.loadSection');
      Route::get("lab_index", [LabsController::class, 'lab_index'])->name("lab_index");
      Route::get("lab_index_data", [LabsController::class, 'lab_index_data'])->name("lab_index_data");
      Route::post('check-unique', [LabsController::class, 'checkUnique'])->name('check_unique');
    });
    Route::resource("labs", LabsController::class);


    Route::group(['prefix' => 'labsession', 'as' => 'labsession.'],function () {
      Route::get("index_list", [LabSessionController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [LabSessionController::class, 'index_data'])->name("index_data");
      Route::get('export', [LabSessionController::class, 'export'])->name('export');
      Route::get('labsession/{' . 'labsession' . '}/edit', [LabSessionController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [LabSessionController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [LabSessionController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [LabSessionController::class, 'forceDelete'])->name('force_delete');
      Route::get('available-slot', [LabSessionController::class, 'availableSlot'])->name('available_slot');
    });
    Route::resource("labsession", LabSessionController::class);
});





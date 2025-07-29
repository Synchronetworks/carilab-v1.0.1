<?php

use Illuminate\Support\Facades\Route;
use Modules\Helpdesk\Http\Controllers\Backend\HelpdesksController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_helpdesks']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Helpdesks Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'helpdesks', 'as' => 'helpdesks.'],function () {
      Route::get("index_list", [HelpdesksController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [HelpdesksController::class, 'index_data'])->name("index_data");
      Route::get('export', [HelpdesksController::class, 'export'])->name('export');
      Route::get('helpdesks/{' . 'helpdesks' . '}/edit', [HelpdesksController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [HelpdesksController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [HelpdesksController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [HelpdesksController::class, 'forceDelete'])->name('force_delete');
      Route::post('helpdesk-action', [HelpdesksController::class, 'action'])->name('action');
      Route::post('helpdesk/{id}', [HelpdesksController::class, 'destroy'])->name('destroy');
      Route::get('helpdesk-closed/{id}', [HelpdesksController::class, 'closed'])->name('closed');
      Route::post('helpdesk-activity/{id}', [HelpdesksController::class, 'activity'])->name('activity');
    });
    Route::resource("helpdesks", HelpdesksController::class);
});




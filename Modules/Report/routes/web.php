<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\Backend\ReportsController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_reports']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Reports Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'reports', 'as' => 'reports.'],function () {
      Route::get("index_list", [ReportsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [ReportsController::class, 'index_data'])->name("index_data");
      Route::get('export', [ReportsController::class, 'export'])->name('earning_report_export');
      Route::get('subscription-export', [ReportsController::class, 'export'])->name('subscription_report_export');
      Route::get('top-testcase-export', [ReportsController::class, 'export'])->name('top_testcase_report_export');
      Route::get('reports/{' . 'reports' . '}/edit', [ReportsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [ReportsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [ReportsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [ReportsController::class, 'forceDelete'])->name('force_delete');
      Route::get("vendor-subscription", [ReportsController::class, 'vendor_subscription'])->name("vendor_subscription");
      Route::get("vendor-subscription-data", [ReportsController::class, 'vendor_subscription_data'])->name("vendor_subscription_data");
      Route::get("top-testcase-booked", [ReportsController::class, 'top_testcase_booked'])->name("top_testcase_booked");
      Route::get("top-testcase-booked-data", [ReportsController::class, 'top_testcase_booked_data'])->name("top_testcase_booked_data");
    });
    Route::resource("reports", ReportsController::class);
});




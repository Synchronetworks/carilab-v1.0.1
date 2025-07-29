<?php

use Illuminate\Support\Facades\Route;
use Modules\Appointment\Http\Controllers\Backend\AppointmentsController;
use Modules\Appointment\Http\Controllers\Backend\PaymentController;



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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_bookings|view_payment_list']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Appointments Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'appointments', 'as' => 'appointments.', 'middleware' => ['permission:view_bookings']],function () {
      Route::get("index_list", [AppointmentsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [AppointmentsController::class, 'index_data'])->name("index_data");
      Route::get('export', [AppointmentsController::class, 'export'])->name('export');
      Route::get('appointments/{' . 'appointments' . '}/edit', [AppointmentsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [AppointmentsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [AppointmentsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [AppointmentsController::class, 'forceDelete'])->name('force_delete');
      Route::post('assign-collector', [AppointmentsController::class, 'assignCollector'])->name('assign_collector');
      Route::get('details/{id}', [AppointmentsController::class, 'appointmentDetails'])->name('details');
      Route::post('accept-reject-appointment', [AppointmentsController::class, 'acceptRejectAppointment'])->name('acceptRejectAppointment');
      Route::post('update-test-case-status', [AppointmentsController::class, 'updateTestCaseStatus'])->name('updateTestCaseStatus');
      Route::get('/invoice/{id}', [AppointmentsController::class, 'createInvoice'])->name('invoice');
      Route::post('/upload-images', [AppointmentsController::class, 'uploadImages'])->name('upload_report');
      Route::post('delete-report', [AppointmentsController::class, 'deleteReport'])->name('delete_report');

    });
    Route::resource("appointments", AppointmentsController::class);

    Route::post('appointments/update-payment-status', 'AppointmentsController@updatePaymentStatus')
    ->name('appointments.update_payment_status');
    
    Route::group(['prefix' => 'payments', 'as' => 'payments.', 'middleware' => ['permission:view_payment_list']],function () {
      Route::get("index_data", [PaymentController::class, 'index_data'])->name("index_data");
      Route::get("cash-payment-list/{payment_type?}", [PaymentController::class, 'cash_payment_list'])->name("cash_payment_list");
      Route::post('restore/{id}', [PaymentController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [PaymentController::class, 'forceDelete'])->name('force_delete');
      Route::get('cash-history/{id?}', [PaymentController::class, 'cashHistory'])->name('cash_history');
      Route::get('cash-index-data/{id}', [PaymentController::class, 'cash_index_data'])->name('cash_index_data');
      Route::get('cash/approve/{id}', [PaymentController::class, 'cashApprove'])->name('approve');
      Route::get('export', [PaymentController::class, 'export'])->name('export');
      Route::get('export/{id?}', [PaymentController::class, 'export'])->name('cash.history.export');


    });
    Route::resource("payments", PaymentController::class);
});





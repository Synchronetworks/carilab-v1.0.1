<?php
use Illuminate\Support\Facades\Route;
use Modules\Appointment\Http\Controllers\Backend\AppointmentsController;
use Modules\Appointment\Http\Controllers\API\AppointmentAPIController;
use Modules\Appointment\Http\Controllers\API\PaymentController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('appointment-list', [ AppointmentAPIController::class, 'getAppointments' ] );
    Route::get('appointment-activity', [ AppointmentAPIController::class, 'getActivities' ] );
    Route::get('appointment-detail', [ AppointmentAPIController::class, 'appointmentDetail' ] );
    Route::post('appointment-update', [ AppointmentsController::class, 'update' ] );
    Route::post('appointment-save', [ AppointmentsController::class, 'store' ] );
    Route::post('appointment-assign', [ AppointmentsController::class, 'assignCollector' ] );
    Route::post('otp-verification', [ AppointmentsController::class, 'otpVerification' ] );
    Route::post('save-payment',[PaymentController::class, 'savePayment']);
    Route::post('transfer-payment',[PaymentController::class, 'transferPayment']);
    Route::get('payment-list',[PaymentController::class, 'paymentList']);
    Route::get('appointment-status', [ AppointmentAPIController::class, 'appointmentStatus' ] );
    Route::get('get-location',[AppointmentAPIController::class, 'getLocation']);
    Route::post('update-location',[AppointmentAPIController::class, 'updateLocation']);
    Route::get('cash-payment-history',[PaymentController::class, 'cashpaymentHistory']);
    Route::get('cash-payment-detail',[PaymentController::class, 'paymentDetail']);
    Route::get('invoice/{id}', [AppointmentsController::class, 'createInvoice']);
    Route::post('collected-test-case', [AppointmentAPIController::class, 'collectedTestCase']);
});

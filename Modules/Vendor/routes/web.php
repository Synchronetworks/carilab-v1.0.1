<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\Http\Controllers\Backend\VendorsController;
use Modules\Vendor\Http\Controllers\Backend\VendorDocumentController;
use Modules\Vendor\Http\Controllers\VendorRegistrationController;
use Modules\Vendor\Http\Controllers\VendorPaymentController;
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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','permission:view_vendor']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Vendors Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'vendors', 'as' => 'vendors.'],function () {
      Route::get("list/{approval_status?}", [VendorsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [VendorsController::class, 'index_data'])->name("index_data");
      Route::get('export', [VendorsController::class, 'export'])->name('export');
      Route::get('vendors/{' . 'vendors' . '}/edit', [VendorsController::class, 'edit'])->name('edit');
      Route::post('bulk-action', [VendorsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('restore/{id}', [VendorsController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [VendorsController::class, 'forceDelete'])->name('force_delete');
      Route::get('changepassword/{id}', [VendorsController::class, 'changepassword'])->name('changepassword');
      Route::post('update-password/{id}', [VendorsController::class, 'updatePassword'])->name('update_password'); 
    });
    Route::resource("vendors", VendorsController::class);
 

});
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth']], function () {
    Route::get('details/{id}', [VendorsController::class, 'details'])->name('vendors.details');
    Route::get('/subscription-history', [VendorsController::class, 'subscriptionHistory'])->name('vendors.subscription-history');
    Route::post('/cancel-subscription', [VendorsController::class, 'cancelSubscription'])->name('vendors.cancelSubscription'); 
    Route::get('/subscriptionUpgradePlan', [VendorPaymentController::class, 'subscriptionUpgradePlan'])->name('subscriptionUpgradePlan');

    Route::group(['prefix' => 'vendordocument','as' => 'vendordocument.'], function () {
          
      Route::get('vendordocument-index-data', [VendorDocumentController::class, 'index_data'])->name('index_data');
      Route::post('vendordocument-bulk-action', [VendorDocumentController::class, 'bulk_action'])->name('bulk-action');
      Route::get('export', [VendorDocumentController::class, 'export'])->name('export');
      Route::post('vendordocument-action', [VendorDocumentController::class, 'action'])->name('action');
      Route::post('vendordocument/{id}', [VendorDocumentController::class, 'destroy'])->name('destroy');
      Route::post('update_required/{id}', [VendorDocumentController::class, 'update_required'])->name('update_required');
      Route::post('restore/{id}', [VendorDocumentController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [VendorDocumentController::class, 'forceDelete'])->name('force_delete');
      Route::post('update-status/{id}', [VendorDocumentController::class, 'update_status'])->name('update_status');

    });
    Route::resource('vendordocument', VendorDocumentController::class);
});
Route::get('/vendor/wizard/step/{step}', [VendorRegistrationController::class, 'showWizardStep'])->name('showWizardStep');
Route::post('/vendor/wizard/step/{step}/store', [VendorRegistrationController::class, 'storeStepData']);
Route::post('/select-plan', [VendorRegistrationController::class, 'selectPlan'])->name('select.plan');
Route::post('/process-payment', [VendorPaymentController::class, 'processPayment'])->name('process-payment');
Route::get('/payment/success', [VendorPaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/subscription-plan', [VendorPaymentController::class, 'subscriptionPlan'])->name('subscriptionPlan');
         
Route::post('/get-payment-details', [VendorRegistrationController::class, 'getPaymentDetails'])->name('get-payment-details');
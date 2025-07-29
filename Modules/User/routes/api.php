<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\API\UserController;
use App\Http\Controllers\Backend\API\UserReportController;


Route::get('device-logout-data', [UserController::class, 'deviceLogout']);
Route::get('logout-all-data', [UserController::class, 'logoutAll']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('profile-details', [UserController::class, 'profileDetails']);
    Route::post('add-patient-member', [UserController::class, 'addOtherMember']);
    Route::get('other-members-list', [UserController::class, 'getOtherMembersList']);
    Route::post('delete-other_member/{id}',[UserController::class, 'deleteOtherMember']);

    Route::post('add-address', [UserController::class, 'addAddress']);
    Route::get('address-list', [UserController::class, 'getAddressList']);
    Route::post('delete-address/{id}',[UserController::class, 'deleteAddress']);
    Route::post('user-update-status',[UserController::class, 'collectorAvailable']);

    Route::get('user-list',[UserController::class, 'userList']);

    Route::post('/save-reports', [UserReportController::class, 'store']);
    Route::post('/update-reports/{id}', [UserReportController::class, 'updateReport']);
    Route::delete('/delete-report/{id}', [UserReportController::class, 'destroy']);
    Route::get('report-list', [UserReportController::class, 'getReportList']);
});
?>

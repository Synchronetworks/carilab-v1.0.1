<?php

use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\BackupController;
use App\Http\Controllers\Backend\NotificationsController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermission;
use App\Http\Controllers\SearchController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\MobileSettingController;
use Modules\Setting\Http\Controllers\Backend\SettingsController;
use Modules\Frontend\Http\Controllers\FrontendController;
use Modules\Vendor\Http\Controllers\VendorRegistrationController;
use App\Http\Controllers\Backend\ActivityLogController;
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

// Auth Routes
require __DIR__ . '/auth.php';
Route::get('storage-link', function () {
    return Artisan::call('storage:link');
});

Route::get('/', function () {
    return redirect(RouteServiceProvider::HOME);
})->middleware('auth');


Route::group(['middleware' => ['auth']], function () {
    Route::get('notification-list', [NotificationsController::class, 'notificationList'])->name('notification.list');
    Route::get('notification-counts', [NotificationsController::class, 'notificationCounts'])->name('notification.counts');
    Route::delete('notification-remove/{id}', [NotificationsController::class, 'notificationRemove'])->name('notification.remove');
    Route::post('bulk-action', [NotificationsController::class, 'bulk_action'])->name('backend.notifications.bulk_action');
});

Route::group(['prefix' => 'app', ['middleware' => ['auth']]], function () {
    // Language Switch
    Route::post('check-in-trash', [SearchController::class, 'check_in_trash'])->name('check-in-trash');
    Route::get('language/{language}', [LanguageController::class, 'switch'])->name('language.switch');
    Route::post('set-user-setting', [BackendController::class, 'setUserSetting'])->name('backend.setUserSetting');
    Route::group(['as' => 'backend.', 'middleware' => ['auth']], function () {
        Route::post('update-player-id', [UserController::class, 'update_player_id'])->name('update-player-id');
        Route::get('get_search_data', [SearchController::class, 'get_search_data'])->name('get_search_data');
        // Sync Role & Permission
        Route::get('/permission-role', [RolePermission::class, 'index'])->name('permission-role.list')->middleware('password.confirm');
        Route::post('/permission-role/store/{role_id}', [RolePermission::class, 'store'])->name('permission-role.store');
        Route::get('/permission-role/reset/{role_id}', [RolePermission::class, 'reset_permission'])->name('permission-role.reset');
        // Role & Permissions Crud
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);

        Route::group(['prefix' => 'module', 'as' => 'module.'], function () {
            Route::get('index_data', [ModuleController::class, 'index_data'])->name('index_data');
            Route::post('update-status/{id}', [ModuleController::class, 'update_status'])->name('update_status');
        });

        Route::resource('module', ModuleController::class);

        /*
          *
          *  Settings Routes
          *
          * ---------------------------------------------------------------------
          */
        Route::group(['middleware' => ['admin']], function () {
            Route::get('settings/{vue_capture?}', [SettingController::class, 'index'])->name('settings')->where('vue_capture', '^(?!storage).*$');
            Route::get('settings-data', [SettingController::class, 'index_data']);
            Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
            Route::get('clear-cache', [SettingController::class, 'clear_cache'])->name('clear-cache');
            Route::post('verify-email', [SettingController::class, 'verify_email'])->name('verify-email');
        });

        /*
        *
        *  Notification Routes
        *
        * ---------------------------------------------------------------------
        */
        Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::get('/index_data', [NotificationsController::class, 'index_data'])->name('index_data');
            Route::get('/markAllAsRead', [NotificationsController::class, 'markAllAsRead'])->name('markAllAsRead');
            Route::delete('/deleteAll', [NotificationsController::class, 'deleteAll'])->name('deleteAll');
            Route::get('/{id}', [NotificationsController::class, 'show'])->name('show');
        });

        /*
        *
        *  Backup Routes
        *
        * ---------------------------------------------------------------------
        */
       

    });

    /*
    *
    * Backend Routes
    * These routes need view-backend permission
    * --------------------------------------------------------------------
    */
    Route::group(['as' => 'backend.', 'middleware' => ['auth']], function () {

        Route::get('/dashboard', [BackendController::class, 'index'])->name('home');
        Route::get('/daterange', [BackendController::class, 'daterange'])->name('daterange');
        Route::get('google-auth', [BackendController::class, 'googleAuth'])->name('google-auth');
        Route::get('/get_revnue_chart_data/{type}', [BackendController::class, 'getRevenuechartData']);
        Route::get('/get_subscriber_chart_data/{type}', [BackendController::class, 'getSubscriberChartData']);
        Route::get('chart-data', [BackendController::class, 'getChartData'])->name('chart-data');
        Route::get('export/{user_type?}', [ActivityLogController::class, 'export'])->name('activityLog.export');
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/data', [ActivityLogController::class, 'index_data'])->name('activity-log.index_data');
        Route::post('/approve/{type}/{id}', [BackendController::class, 'approve'])->name('approve');
        Route::get('activity-log/collector', [ActivityLogController::class, 'index'])->name('activity-log-collector.index')->defaults('user_type', 'collector');
        Route::get('activity-log/vendor', [ActivityLogController::class, 'index'])->name('activity-log-vendor.index')->defaults('user_type', 'vendor');
        Route::group(['prefix' => ''], function () {

            /*
            *
            *  Users Routes
            *
            * ---------------------------------------------------------------------
            */
            Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                Route::get('user-list', [UserController::class, 'user_list'])->name('user_list');
                Route::get('emailConfirmationResend/{id}', [UserController::class, 'emailConfirmationResend'])->name('emailConfirmationResend');
                Route::post('create-customer', [UserController::class, 'create_customer'])->name('create_customer');
                Route::post('information', [UserController::class, 'update'])->name('information');
                Route::post('change-password', [UserController::class, 'change_password'])->name('change_password');

            });
        });
        Route::get('my-profile/{vue_capture?}', [UserController::class, 'myProfile'])->name('my-profile')->where('vue_capture', '^(?!storage).*$');
        Route::get('my-info', [UserController::class, 'authData'])->name('authData');
        Route::post('my-profile/change-password', [UserController::class, 'change_password'])->name('change_password');
        Route::get('app-configuration', [App\Http\Controllers\Backend\API\SettingController::class, 'appConfiguraton']);
        Route::get('data-configuration', [App\Http\Controllers\Backend\API\SettingController::class, 'Configuraton']);
        Route::post('store-access-token', [SettingController::class, 'storeToken']);
        Route::post('token-revoke', [SettingController::class, 'revokeToken']);


        
    });

    Route::post('/auth/google', [SettingController::class, 'googleId']);
    Route::get('callback', [SettingController::class, 'handleGoogleCallback']);
    Route::post('/store-access-token', [SettingController::class, 'storeToken']);
    Route::get('google-key', [SettingController::class, 'googleKey']);
    Route::get('currencies_data', [SettingsController::class, 'getCurrencyData'])->name('backend.currencies.getCurrencyData');


    Route::group(['as' => 'backend.'], function () {
        Route::post('/clear-cache-config', function () {
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');
            return response()->json(['message' => 'Cache and Config cleared']);
        })->name('config_clear');
    });

});

Route::get('vendor-registration', [VendorRegistrationController::class, 'create'])->name('vendor-registration');
Route::get('subscription-plan', [VendorPaymentController::class, 'subscriptionPlan'])->name('subscriptionPlan');
Route::post('/select-plansubscribe', [VendorPaymentController::class, 'selectPlan'])->name('select.plan');

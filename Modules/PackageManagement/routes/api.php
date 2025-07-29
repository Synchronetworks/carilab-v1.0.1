<?php

use Illuminate\Support\Facades\Route;
use Modules\PackageManagement\Http\Controllers\API\PackageManagementsController;


Route::get('package-list',[ PackageManagementsController::class, 'packageList' ]);
Route::get('package-detail',[ PackageManagementsController::class, 'packageDetail' ]);
Route::get('package-testcase',[ PackageManagementsController::class, 'testCaseListByPackage' ]);

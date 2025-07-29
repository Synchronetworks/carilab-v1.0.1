<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\API\CategoriesController;


Route::get('category-list',[ CategoriesController::class, 'categoryList' ]);
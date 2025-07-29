<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\Http\Controllers\API\ReviewsController;
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('add-review',[ ReviewsController::class, 'addReview' ]);
    Route::post('delete-review/{id}',[ReviewsController::class, 'deleteReview']);
});

Route::get('review-list',[ ReviewsController::class, 'reviewList' ]);

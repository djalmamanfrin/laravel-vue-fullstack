<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::apiResource('/image', ImageController::class)
            ->only(['index', 'store', 'destroy']);
        Route::get('/categories/{categoryId}/images', [CategoryController::class, 'images']);

        Route::apiResource('/category', CategoryController::class)
            ->only(['index', 'destroy']);
    });

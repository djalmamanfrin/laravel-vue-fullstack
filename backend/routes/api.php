<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::apiResource('board', BoardController::class)
            ->only(['index', 'store', 'destroy']);
        Route::get('/board/{boardId}', [BoardController::class, 'get']);
        Route::patch('/board/{boardId}', [BoardController::class, 'update']);
        Route::get('/board/{boardId}/recent-changes', [BoardController::class, 'recentChanges']);
        Route::post('/board/{boardId}/collection', [BoardController::class, 'createCollection']);
        Route::patch('/board/{boardId}/collection/{collectionId}', [BoardController::class, 'reorderCollection']);
        Route::delete('/board/{boardId}/collection/{collectionId}', [BoardController::class, 'deleteCollection']);

        Route::apiResource('/image', ImageController::class)
            ->only(['index', 'store', 'destroy']);
        Route::get('/image/{imageId}', [ImageController::class, 'get']);
        Route::patch('/image/{imageId}', [ImageController::class, 'update']);
    });

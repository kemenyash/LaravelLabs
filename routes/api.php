<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

// Apply middleware to the entire route group
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cars', [CarController::class, 'index']);
    Route::post('/cars', [CarController::class, 'store']);
    Route::put('/cars/{id}', [CarController::class, 'update']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

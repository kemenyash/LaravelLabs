<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

// Ваш маршрути
Route::get('/cars', [CarController::class, 'index']);
Route::post('/cars', [CarController::class, 'store']);
Route::put('/cars/{id}', [CarController::class, 'update']);
Route::delete('/cars/{id}', [CarController::class, 'destroy']);
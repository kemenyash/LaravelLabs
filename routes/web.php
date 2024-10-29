<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

//API controllers
Route::get('/api/cars', [CarController::class, 'index']);
Route::post('/api/cars', [CarController::class, 'store']);
Route::put('/api/cars/{code}', [CarController::class, 'update']);


Route::get('/', function () {
    return view('welcome');
});

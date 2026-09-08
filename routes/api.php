<?php

use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/weather/history', [WeatherController::class, 'history']);
Route::get('/weather/history/{id}', [WeatherController::class, 'show']);
Route::delete('/weather/history/{id}', [WeatherController::class, 'destroy']);
Route::get('/weather', [WeatherController::class, 'current']);

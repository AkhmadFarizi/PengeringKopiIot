<?php

use App\Http\Controllers\SensorLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/sensor-logs', [SensorLogController::class, 'index']);
Route::get('/api/sensor-logs/latest', [SensorLogController::class, 'getLatestData']);
Route::get('/api/sensor-logs', [SensorLogController::class, 'getHistoryData']);
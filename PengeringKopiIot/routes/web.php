<?php

use App\Http\Controllers\SensorLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SensorLogController::class, 'index'] )->name('home');
Route::get('/sensorlogs/data', [SensorLogController::class, 'data'])->name('sensorlogs.data');


Route::get('/sensor-logs', [SensorLogController::class, 'index']);
Route::get('/api/sensor-logs/latest', [SensorLogController::class, 'getLatestData']);
Route::get('/api/sensor-logs', [SensorLogController::class, 'getHistoryData']);
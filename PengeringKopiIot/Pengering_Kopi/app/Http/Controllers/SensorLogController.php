<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use App\Http\Requests\StoreSensorLogRequest;
use App\Http\Requests\UpdateSensorLogRequest;

class SensorLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sensor-log');
    }

    public function getLatestData()
    {
        $latest = SensorLog::latest()->first();
        return response()->json($latest);
    }

    public function getHistoryData()
    {
        $history = SensorLog::orderBy('created_at', 'asc') // Urutkan dari yang terlama
            ->limit(100)
            ->get()
            ->map(function ($item) {
                return [
                    'timestamp' => $item->created_at->getTimestamp() * 1000, // Convert to milliseconds
                    'suhu' => $item->suhu,
                    'rpm' => $item->rpm,
                    'arus' => $item->arus,
                    'relayFan' => $item->relayFan,
                    'relayHeater' => $item->relayHeater
                ];
            });

        return response()->json($history);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'suhu' => 'required|numeric',
            'rpm' => 'required|numeric',
            'arus' => 'required|numeric',
            'relayFan' => 'required|in:ON,OFF',
            'relayHeater' => 'required|in:ON,OFF',
        ]);

        // Simpan ke database
        SensorLog::create([
            'suhu' => $request->suhu,
            'rpm' => $request->rpm,
            'arus' => $request->arus,
            'relayFan' => $request->relayFan,
            'relayHeater' => $request->relayHeater,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data sensor berhasil disimpan ke database'
        ]);
    }
}

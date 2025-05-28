<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use App\Http\Requests\StoreSensorLogRequest;
use App\Http\Requests\UpdateSensorLogRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;




class SensorLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dates = SensorLog::selectRaw('DATE(created_at) as date_only')
            ->groupBy('date_only')
            ->orderBy('date_only', 'desc')
            ->pluck('date_only');

        return view('welcome', compact('dates'));
    }



    public function data(Request $request)
    {
        $query = SensorLog::query();

        // Jika ada filter tanggal, gunakan filter tersebut
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default ke hari ini
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
        }

        // Apply filter tanggal ke query
        $query->whereBetween('created_at', [$start, $end]);

        // Hitung jumlah data
        $count = $query->count();

        // Jika tidak ada data, kirim satu baris dummy dengan pesan
        if ($count == 0) {
            return DataTables::of(collect([[
                'id' => '-',
                'suhu' => 'Data kosong pada tanggal ' . $start->format('d-m-Y') . ' sampai ' . $end->format('d-m-Y'),
                'rpm' => '-',
                'arus' => '-',
                'relayFan' => '-',
                'relayHeater' => '-',
                'created_at' => '-',
            ]]))->make(true);
        }

        return DataTables::of($query)
            ->editColumn('suhu', fn($row) => number_format($row->suhu, 2) . ' °C')
            ->editColumn('rpm', fn($row) => $row->rpm . ' RPM')
            ->editColumn('arus', fn($row) => number_format($row->arus, 2) . ' mA')
            ->editColumn('relayFan', fn($row) => strtoupper($row->relayFan))
            ->editColumn('relayHeater', fn($row) => strtoupper($row->relayHeater))
            ->editColumn('created_at', fn($row) => Carbon::parse($row->created_at)->format('d-m-Y H:i:s'))
            ->make(true);
    }




    public function getLatestData()
    {
        $latest = SensorLog::latest()->first();
        return response()->json($latest);
    }

    public function getHistoryData()
    {
        $history = SensorLog::orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->limit(50) // Ambil 100 data terakhir
            ->get()
            ->map(function ($item) {
                return [
                    'timestamp' => $item->created_at->getTimestamp() * 1000, // Convert to milliseconds
                    'suhu' => (float)$item->suhu,
                    'rpm' => (float)$item->rpm,
                    'arus' => (float)$item->arus,
                    'relayFan' => $item->relayFan,
                    'relayHeater' => $item->relayHeater
                ];
            });

        return response()->json($history);
    }
}

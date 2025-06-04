<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;

class SensorDataController extends Controller
{
    public function latestByUser(Request $request)
    {
        $user = $request->user();

        // Ambil semua device_id milik user
        $deviceIds = $user->devices()->pluck('id');

        // Ambil data sensor terbaru dari device milik user
        $latestSensor = SensorData::whereIn('device_id', $deviceIds)
            ->latest('timestamp')
            ->first();

        if (!$latestSensor) {
            return response()->json([
                'message' => 'Tidak ada data sensor tersedia.',
            ], 404);
        }

        return response()->json([
            'turbidity' => $latestSensor->turbidity,
            'timestamp' => $latestSensor->timestamp,
            'device_id' => $latestSensor->device_id,
        ]);
    }
}

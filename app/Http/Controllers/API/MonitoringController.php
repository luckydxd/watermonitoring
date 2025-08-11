<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function usage(Request $request)
    {
        $user = $request->user();
        $device = $user->devices()->first();

        if (!$device) {
            return response()->json(['message' => 'Tidak ada device'], 404);
        }

        $usages = SensorData::selectRaw('DATE(timestamp) as date, SUM(flow_rate) as total_usage')
            ->where('device_id', $device->id)
            ->where('timestamp', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($usages);
    }
}

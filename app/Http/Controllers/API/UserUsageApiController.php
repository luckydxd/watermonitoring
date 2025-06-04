<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WaterConsumptionLog;
use App\Models\DeviceAssignment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserUsageApiController extends Controller
{
    public function getUserConsumption()
    {
        $data = WaterConsumptionLog::query()
            ->where('user_id', auth()->id())
            ->select(['id', 'date', 'total_consumption'])
            ->orderBy('date', 'DESC');


        return DataTables::of($data)->make(true);
    }

    public function usageByUser(Request $request)
    {
        try {
            $data = WaterConsumptionLog::where('user_id', $request->user()->id)
                ->select(['id', 'date', 'total_consumption'])
                ->orderBy('date', 'DESC')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Water consumption data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function usageByMonth(Request $request)
    {
        $userId = $request->user()->id;
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Gunakan cursor() untuk memory efficiency pada data besar
        $monthlyTotal = 0;
        $days = [];
        $recordCount = 0;

        WaterConsumptionLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select(['date', 'total_consumption'])
            ->orderBy('date')
            ->cursor()
            ->each(function ($item) use (&$monthlyTotal, &$days, &$recordCount) {
                $monthlyTotal += $item->total_consumption;
                $days[] = [
                    'date' => $item->date->format('Y-m-d'),
                    'day' => $item->date->day,
                    'consumption' => (float) $item->total_consumption
                ];
                $recordCount++;
            });

        return response()->json([
            'success' => true,
            'data' => $days,
            'statistics' => [
                'monthly_total' => round($monthlyTotal, 2),
                'average_daily' => $recordCount > 0 ? round($monthlyTotal / $recordCount, 2) : 0,
                'days_recorded' => $recordCount
            ]
        ]);
    }
}

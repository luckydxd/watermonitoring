<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\WaterConsumptionLog;
use App\Models\FlowPressureSensor;
use App\Models\WaterQualitySensor;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class UserDashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $devices = Device::join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->where('device_assignments.user_id', auth()->id())
            ->where('device_assignments.is_active', true)
            ->select('devices.*')
            ->get();

        $onlineDevicesCount = 0;
        $totalDevicesCount = $devices->count();
        $hasDevice = $totalDevicesCount > 0;

        $chartData = $this->getInitialChartData(auth()->user());

        if ($hasDevice) {
            $deviceIds = $devices->pluck('id');

            // Ambil timestamp terakhir dari SETIAP jenis sensor dalam satu query
            $latestFlowReadings = FlowPressureSensor::select('device_id', DB::raw('MAX(measured_at) as last_seen'))
                ->whereIn('device_id', $deviceIds)
                ->groupBy('device_id')
                ->pluck('last_seen', 'device_id');

            $latestQualityReadings = WaterQualitySensor::select('device_id', DB::raw('MAX(measured_at) as last_seen'))
                ->whereIn('device_id', $deviceIds)
                ->groupBy('device_id')
                ->pluck('last_seen', 'device_id');


            foreach ($devices as $device) {
                if ($device->operational_status['status_text'] === 'Active') {
                    $onlineDevicesCount++;
                }
            }
        }

        return view('user.dashboard', compact(
            'onlineDevicesCount',
            'totalDevicesCount',
            'hasDevice',
            // 'consumptionChartData'
            'chartData'
        ));
    }


    private function getInitialChartData($user, $range = 'last7')
    {
        $activeDeviceIds = $user->deviceAssignments()
            ->where('is_active', true)
            ->pluck('device_id')
            ->toArray();

        if (empty($activeDeviceIds)) {
            return [
                'consumption' => [],
                'flowRate' => [],
                'pressure' => [],
            ];
        }

        // rentang tanggal (default 7 hari terakhir)
        $now = Carbon::now();
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        // Ambil data pembacaan volume di akhir setiap hari, plus satu hari sebelumnya
        $endOfDayReadings = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
            ->whereBetween('measured_at', [$startDate->copy()->subDay(), $endDate])
            ->select(
                DB::raw('DATE(measured_at) as date'),
                DB::raw('MAX(volume) as end_of_day_volume')
            )->groupBy('date')->orderBy('date')->get();

        // Hitung selisih harian 
        $consumptionData = collect();
        for ($i = 1; $i < $endOfDayReadings->count(); $i++) {
            $consumption = $endOfDayReadings[$i]->end_of_day_volume - $endOfDayReadings[$i - 1]->end_of_day_volume;
            if ($consumption >= 0) {
                $consumptionData->push(['x' => $endOfDayReadings[$i]->date, 'y' => (float)round($consumption, 2)]);
            }
        }

        // Data FLOW RATE 
        $flowData = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
            ->whereBetween('measured_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(measured_at, '%Y-%m-%d %H:00:00') as date"),
                DB::raw("ROUND(AVG(flow_rate), 2) as value")
            )
            ->groupBy('date')->orderBy('date')->get();

        // Data PRESSURE 
        $pressureData = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
            ->whereBetween('measured_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(measured_at, '%Y-%m-%d %H:00:00') as date"),
                DB::raw("ROUND(AVG(pressure), 2) as value")
            )
            ->groupBy('date')->orderBy('date')->get();

        return [
            'consumption' => $consumptionData,
            'flowRate'    => $flowData->map(fn($item) => ['x' => $item->date, 'y' => (float)$item->value]),
            'pressure'    => $pressureData->map(fn($item) => ['x' => $item->date, 'y' => (float)$item->value]),
        ];
    }



    public function getTodayUsage()
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        $hasDevice = DeviceAssignment::where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();

        if (!$hasDevice) {
            return response()->json([
                'error' => 'Anda tidak memiliki perangkat yang terhubung',
                'total_usage' => 0
            ], 200);
        }

        return Cache::remember("user_{$user->id}_usage_{$today}", now()->addHours(1), function () use ($user, $today) {
            return response()->json([
                'total_usage' => WaterConsumptionLog::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->sum('total_consumption') ?? 0
            ]);
        });
    }
}

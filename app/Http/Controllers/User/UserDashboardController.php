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
        // Langkah 1: Ambil semua device yang aktif milik user (ini sudah benar)
        $devices = Device::join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->where('device_assignments.user_id', auth()->id())
            ->where('device_assignments.is_active', true)
            ->select('devices.*')
            ->get();

        $onlineDevicesCount = 0;
        $totalDevicesCount = $devices->count();
        $hasDevice = $totalDevicesCount > 0;

        $consumptionChartData = $this->getInitialConsumptionData(auth()->user(), 'last7');


        if ($hasDevice) {
            // Ambil semua ID perangkat aktif untuk query yang efisien
            $deviceIds = $devices->pluck('id');

            // Langkah 2: Ambil timestamp terakhir dari SETIAP jenis sensor dalam satu query
            // Ini jauh lebih efisien daripada melakukan query di dalam loop (menghindari N+1 problem)
            $latestFlowReadings = FlowPressureSensor::select('device_id', DB::raw('MAX(measured_at) as last_seen'))
                ->whereIn('device_id', $deviceIds)
                ->groupBy('device_id')
                ->pluck('last_seen', 'device_id');

            $latestQualityReadings = WaterQualitySensor::select('device_id', DB::raw('MAX(measured_at) as last_seen'))
                ->whereIn('device_id', $deviceIds)
                ->groupBy('device_id')
                ->pluck('last_seen', 'device_id');

            // Langkah 3: Loop melalui setiap device dan cek statusnya berdasarkan data sensor terakhir
            foreach ($devices as $device) {
                // Cari timestamp terakhir untuk device ini dari kedua jenis sensor
                $lastSeenFlow = $latestFlowReadings->get($device->id);
                $lastSeenQuality = $latestQualityReadings->get($device->id);

                // Tentukan mana yang paling baru di antara keduanya
                $latestTimestamp = null;
                if ($lastSeenFlow && $lastSeenQuality) {
                    $latestTimestamp = Carbon::parse($lastSeenFlow)->isAfter(Carbon::parse($lastSeenQuality)) ? $lastSeenFlow : $lastSeenQuality;
                } else {
                    $latestTimestamp = $lastSeenFlow ?? $lastSeenQuality;
                }

                // Jika device pernah mengirim data sensor
                if ($latestTimestamp) {
                    $diffMinutes = Carbon::parse($latestTimestamp)->diffInMinutes(now());
                    $status = strtolower($device->status);

                    // Kriteria device dianggap "Online"
                    if ($status === 'active' && $diffMinutes <= 60) {
                        $onlineDevicesCount++;
                    }
                }
            }
        }

        // Variabel yang dikirim ke view tetap sama, jadi tidak perlu mengubah file blade
        return view('user.dashboard', compact(
            'onlineDevicesCount',
            'totalDevicesCount',
            'hasDevice',
            'consumptionChartData'
        ));
    }
    private function getInitialConsumptionData($user, $range = 'last7')
    {
        // Langkah 1: Dapatkan ID perangkat aktif yang memiliki data volume
        $activeFlowDeviceAssignment = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select('device_assignments.device_id')
            ->first();

        // Jika tidak ada perangkat yang relevan, kembalikan data kosong
        if (!$activeFlowDeviceAssignment) {
            return ['dates' => [], 'consumption' => []];
        }
        $deviceId = $activeFlowDeviceAssignment->device_id;

        // Langkah 2: Tentukan rentang tanggal berdasarkan parameter
        $now = Carbon::now();
        switch ($range) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last30':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'thisMonth':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'lastMonth':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'last7':
            default:
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        // Langkah 3: Query ke tabel flow_pressure_sensors dengan logika MAX - MIN
        $consumptionData = FlowPressureSensor::where('device_id', $deviceId)
            ->whereBetween('measured_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(measured_at) as date'),
                DB::raw('MAX(volume) - MIN(volume) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Langkah 4: Format data agar sesuai dengan yang diharapkan oleh JavaScript
        return [
            'dates' => $consumptionData->pluck('date'),
            'consumption' => $consumptionData->pluck('total')->map(fn($value) => (float) $value),
        ];
    }



    public function getTodayUsage()
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        // Cek apakah user memiliki device
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

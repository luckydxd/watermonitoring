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
                    if ($status === 'active' && $diffMinutes <= 15) {
                        $onlineDevicesCount++;
                    }
                }
            }
        }

        // Variabel yang dikirim ke view tetap sama, jadi tidak perlu mengubah file blade
        return view('user.dashboard', compact(
            'onlineDevicesCount',
            'totalDevicesCount',
            'hasDevice'
        ));
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
